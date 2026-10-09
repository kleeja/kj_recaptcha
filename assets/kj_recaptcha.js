/*
 * kj_recaptcha: shows the reCAPTCHA of the plugin in the forms of Kleeja, and gives every submission a fresh answer.
 *
 * The plugin prints <div class="kj-recaptcha"> in a form, in the place of the security code of Kleeja, with:
 *   data-type     1 the "I'm not a robot" checkbox, 2 the invisible badge of v2, 3 a score of v3
 *   data-sitekey  the site key
 *   data-hl       the language of the page
 *   data-action   the name of the form, v3 sends it with the answer and the server checks it
 *
 * The answer is the g-recaptcha-response field of the form, which Kleeja verifies with Google.
 * An answer is good for one check within two minutes, so the invisible badge and v3 ask for it when the form is sent.
 */
(function () {
    "use strict";

    // the script comes with every box, it runs once
    if (window.kjRecaptcha) {
        return;
    }

    window.kjRecaptcha = true;

    var boxes = Array.prototype.slice.call(document.querySelectorAll(".kj-recaptcha[data-sitekey]"));

    if (!boxes.length) {
        return;
    }

    var type = boxes[0].getAttribute("data-type");
    var siteKey = boxes[0].getAttribute("data-sitekey");
    var language = boxes[0].getAttribute("data-hl") || "";
    var ready = false;

    function api() {
        return ready && window.grecaptcha ? window.grecaptcha : null;
    }

    // the dark mode of the bootstrap style and of the control panel
    function theme() {
        return document.documentElement.getAttribute("data-bs-theme") === "dark" ? "dark" : "light";
    }

    function boxOf(form) {
        return form && form.querySelector ? form.querySelector(".kj-recaptcha[data-sitekey]") : null;
    }

    function hasFiles(form) {
        return Array.prototype.some.call(form.querySelectorAll('input[type="file"]'), function (input) {
            return !!(input.files && input.files.length);
        });
    }

    // v3 has no widget, its answer goes in a field of its own
    function answerField(box) {
        var field = box.querySelector('input[name="g-recaptcha-response"]');

        if (!field) {
            field = document.createElement("input");
            field.type = "hidden";
            field.name = "g-recaptcha-response";
            box.appendChild(field);
        }

        return field;
    }

    // the form goes again, with its answer, through the button that sent it
    function send(box) {
        var form = box.closest("form");
        var submitter = box.kjSubmitter;

        box.kjSubmitter = null;
        box.kjPending = false;

        if (!form) {
            return;
        }

        box.kjReady = true;

        if (typeof form.requestSubmit === "function") {
            form.requestSubmit(submitter && submitter.form === form ? submitter : null);

            return;
        }

        // form.submit() sends no submit event and leaves the button out,
        // and the name of the button is what tells Kleeja that the form was sent
        box.kjReady = false;

        if (submitter && submitter.name) {
            var hidden = document.createElement("input");

            hidden.type = "hidden";
            hidden.name = submitter.name;
            hidden.value = submitter.value;
            form.appendChild(hidden);
        }

        form.submit();
    }

    // v2: a widget in every box, the invisible one answers through its callback
    window.kjRecaptchaOnload = function () {
        boxes.forEach(function (box) {
            var options = { sitekey: siteKey, theme: theme() };

            if (type === "2") {
                options.size = "invisible";
                options.badge = "inline";
                options.callback = function () {
                    send(box);
                };
                // Google can't be reached: the form goes without an answer, and Kleeja says that it failed
                options["error-callback"] = function () {
                    send(box);
                };
            }

            box.kjWidget = window.grecaptcha.render(box, options);
        });

        ready = true;
    };

    // the forms that ask for an answer when they are sent, before the scripts of the style see them,
    // like the upload form of the default style that sends the files in the background
    document.addEventListener(
        "submit",
        function (event) {
            var form = event.target;
            var box = boxOf(form);

            if (!box || type === "1") {
                return;
            }

            // this submission has its answer
            if (box.kjReady) {
                box.kjReady = false;

                return;
            }

            var recaptcha = api();

            // Google's script didn't load, it is blocked or the visitor is offline:
            // the form goes as it is, and Kleeja says that the verification failed
            if (!recaptcha) {
                return;
            }

            // the upload form says itself that no file is chosen, there is nothing to verify yet
            if (form.querySelector('input[type="file"]') && !hasFiles(form)) {
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();
            box.kjSubmitter = event.submitter || box.kjSubmitter || null;

            if (type === "2") {
                // a challenge that the visitor closed opens again with the next click,
                // a new submission needs a new answer
                if (!box.kjPending) {
                    recaptcha.reset(box.kjWidget);
                }

                box.kjPending = true;
                recaptcha.execute(box.kjWidget);

                return;
            }

            if (box.kjPending) {
                return;
            }

            box.kjPending = true;

            recaptcha.ready(function () {
                recaptcha.execute(siteKey, { action: box.getAttribute("data-action") || "submit" }).then(
                    function (token) {
                        answerField(box).value = token;
                        send(box);
                    },
                    function () {
                        answerField(box).value = "";
                        send(box);
                    },
                );
            });
        },
        true,
    );

    // the default style shows the links of an upload on the same page,
    // the checkbox was used then, and the next upload needs it again
    document.addEventListener("kleeja:content", function () {
        var recaptcha = api();

        if (!recaptcha || type !== "1") {
            return;
        }

        boxes.forEach(function (box) {
            recaptcha.reset(box.kjWidget);
        });
    });

    var script = document.createElement("script");

    script.src =
        "https://www.google.com/recaptcha/api.js?" +
        (type === "3" ? "render=" + encodeURIComponent(siteKey) : "onload=kjRecaptchaOnload&render=explicit") +
        (language ? "&hl=" + encodeURIComponent(language) : "");
    script.async = true;
    script.defer = true;

    if (type === "3") {
        script.onload = function () {
            ready = true;
        };
    }

    document.head.appendChild(script);
})();
