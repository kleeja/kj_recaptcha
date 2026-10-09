<?php
//
// kj_recaptcha, its guide on the help page of the control panel
// English
//
// The guide is built by the admin_help_guides hook in init.php, the texts here can have HTML.
//  - a list is numbered: KJ_RECAPTCHA_HELP_TIP_1, KJ_RECAPTCHA_HELP_TIP_2 ... it ends at the first missing number,
//    so an item can be added or removed without touching the code
//  - KJ_RECAPTCHA_HELP_{LIST}_TITLE is the title of the list, a list without it takes the title that Kleeja gives its type
//  - questions and answers are KJ_RECAPTCHA_HELP_FAQ_Q_1 and KJ_RECAPTCHA_HELP_FAQ_A_1
//  - names in <strong> are written as the control panel shows them: the words of Kleeja are in lang/en/,
//    the words of the plugin in kj_recaptcha_translations() of init.php
//

return [
    'KJ_RECAPTCHA_HELP_TITLE' => 'reCAPTCHA Protection',
    'KJ_RECAPTCHA_HELP_INTRO' =>
        'Stop bots and spam with Google reCAPTCHA. The plugin replaces the Kleeja security code on your forms and protects the control panel login, with the reCAPTCHA v2 checkbox, the invisible v2 badge or reCAPTCHA v3.',

    //
    // what the plugin does
    //
    'KJ_RECAPTCHA_HELP_FEATURE_1' =>
        'When <strong>Enable Captcha in Kleeja</strong> is on, the login, registration, password recovery, <strong>Contact Us</strong> and <strong>Report</strong> forms ask for reCAPTCHA instead of the security code.',
    'KJ_RECAPTCHA_HELP_FEATURE_2' =>
        'When <strong>Enable safety code for downloads</strong> is on, the upload form asks for reCAPTCHA before it accepts files.',
    'KJ_RECAPTCHA_HELP_FEATURE_3' =>
        'The control panel login always asks for reCAPTCHA, instead of the code that Kleeja shows after a wrong password.',
    'KJ_RECAPTCHA_HELP_FEATURE_4' =>
        'Three types: the <strong>“I\'m not a robot” checkbox</strong>, the <strong>invisible badge</strong> that shows a challenge only when Google suspects a bot, and <strong>v3</strong>, which never shows a challenge and rejects the submissions that score below 0.5.',
    'KJ_RECAPTCHA_HELP_FEATURE_5' =>
        'Your server verifies every answer with Google using your secret key, so a bot can\'t skip the check by sending the form directly.',
    'KJ_RECAPTCHA_HELP_FEATURE_6' =>
        'reCAPTCHA follows the language of the page, and the checkbox follows the dark mode of the bootstrap style and of the control panel.',

    //
    // how to use it
    //
    'KJ_RECAPTCHA_HELP_STEP_TITLE' => 'Set up reCAPTCHA',
    'KJ_RECAPTCHA_HELP_STEP_1' =>
        'Open the <a href="https://www.google.com/recaptcha/admin/create" target="_blank" rel="noopener">reCAPTCHA admin console</a> and sign in with your Google account.',
    'KJ_RECAPTCHA_HELP_STEP_2' =>
        'Give the key a label, and choose <strong>Score based (v3)</strong>, or <strong>Challenge (v2)</strong> with <strong>“I\'m not a robot” Checkbox</strong> or <strong>Invisible reCAPTCHA badge</strong>.',
    'KJ_RECAPTCHA_HELP_STEP_3' =>
        'Add the domain of your site, like <code>example.com</code>, without <code>https://</code> or a path. Google keeps every key in a Google Cloud project: choose one, or let Google create it.',
    'KJ_RECAPTCHA_HELP_STEP_4' =>
        'Submit, then copy the <strong>site key</strong> and the <strong>secret key</strong>.',
    'KJ_RECAPTCHA_HELP_STEP_5' =>
        'In Kleeja, open <strong>Settings</strong>, then <strong>reCAPTCHA settings</strong>. Choose the <strong>reCAPTCHA type</strong> that matches your keys, paste the <strong>Site key</strong> and the <strong>Secret key</strong>, and click <strong>Update Settings</strong>.',
    'KJ_RECAPTCHA_HELP_STEP_6' =>
        'Make sure <strong>Enable Captcha in Kleeja</strong> (in <strong>Interface and design settings</strong>) and <strong>Enable safety code for downloads</strong> (in <strong>Upload settings</strong>) are on for the forms you want to protect.',
    'KJ_RECAPTCHA_HELP_STEP_7' =>
        'Open the login page of your site in a private window, and sign in to check that reCAPTCHA works before you sign out of the control panel.',

    //
    // common questions
    //
    'KJ_RECAPTCHA_HELP_FAQ_Q_1' => 'Which type should I choose?',
    'KJ_RECAPTCHA_HELP_FAQ_A_1' =>
        'The checkbox is the one visitors know best, and every visitor can pass it by solving a challenge. The invisible badge keeps the form clean, and shows a challenge only when Google is unsure. v3 never interrupts visitors, but a visitor with a low score has no challenge to solve, and can\'t send the form at all. If real visitors are rejected with v3, switch to a v2 type.',
    'KJ_RECAPTCHA_HELP_FAQ_Q_2' => 'Every form says that the reCAPTCHA verification failed. Why?',
    'KJ_RECAPTCHA_HELP_FAQ_A_2' =>
        'Check that the <strong>reCAPTCHA type</strong> matches the type of the keys (a v3 key doesn\'t work as v2, and a checkbox key doesn\'t work as an invisible badge), that the domain of your site is in the domain list of the key, and that your server can reach <code>www.google.com</code> over HTTPS. With <code>DEV_STAGE</code> in <code>config.php</code>, Kleeja writes the reason that Google gives to <code>cache/kleeja_log.log</code>.',
    'KJ_RECAPTCHA_HELP_FAQ_Q_3' => 'Why doesn\'t the Contact Us form show reCAPTCHA?',
    'KJ_RECAPTCHA_HELP_FAQ_A_3' =>
        'reCAPTCHA appears only where Kleeja would ask for its security code. Turn on <strong>Enable Captcha in Kleeja</strong> for the login, registration, password recovery, <strong>Contact Us</strong> and <strong>Report</strong> forms, and <strong>Enable safety code for downloads</strong> for the upload form.',
    'KJ_RECAPTCHA_HELP_FAQ_Q_4' => 'Does Google charge for reCAPTCHA?',
    'KJ_RECAPTCHA_HELP_FAQ_A_4' =>
        'Google keeps reCAPTCHA keys in Google Cloud projects, with 10,000 free checks a month. Each login, registration, message or upload is one check. If your site needs more, see the reCAPTCHA page of your project in the Google Cloud console.',
    'KJ_RECAPTCHA_HELP_FAQ_Q_5' => 'I can\'t sign in to the control panel. What should I do?',
    'KJ_RECAPTCHA_HELP_FAQ_A_5' =>
        'Add <code>define(\'STOP_CAPTCHA\', true);</code> to <code>config.php</code>. The control panel login then skips reCAPTCHA. Sign in, fix the keys or set the type to <strong>Disabled</strong>, then remove the line.',

    //
    // beside the guide
    //
    'KJ_RECAPTCHA_HELP_TIP_1' =>
        'To test reCAPTCHA on your own computer, add <code>localhost</code> to the domain list of a separate key, and keep your real keys for your site.',
    'KJ_RECAPTCHA_HELP_TIP_2' =>
        'With v3, the reCAPTCHA page of your project shows the scores of each form, by the names <code>login</code>, <code>register</code>, <code>get_pass</code>, <code>call</code>, <code>report</code>, <code>upload</code> and <code>admin_login</code>.',
    'KJ_RECAPTCHA_HELP_TIP_3' =>
        'Until you fill in both keys and choose a type, Kleeja keeps its own security code, so your forms are never left unprotected.',

    'KJ_RECAPTCHA_HELP_WARNING_1' =>
        'Keep the secret key private. If it leaks, delete the key in the reCAPTCHA admin console and create a new one.',
    'KJ_RECAPTCHA_HELP_WARNING_2' =>
        'If your server can\'t reach Google, every protected form is rejected, including the control panel login.',
    'KJ_RECAPTCHA_HELP_WARNING_3' =>
        'Visitors load the reCAPTCHA script from Google on the protected forms. Mention reCAPTCHA in your privacy policy, and keep the reCAPTCHA badge visible, as Google\'s terms require.',
    'KJ_RECAPTCHA_HELP_WARNING_4' =>
        'The DragDrop style uploads files without the reCAPTCHA answer. With that style, keep <strong>Enable safety code for downloads</strong> off, or uploads are rejected.',

    //
    // in the guide of the settings, whose page has the tab of the plugin
    //
    'KJ_RECAPTCHA_HELP_NOTE' =>
        'The <strong>reCAPTCHA settings</strong> tab belongs to the <strong>reCAPTCHA Protection</strong> plugin. <a href="#help-kj_recaptcha">Read its guide</a>.',
];
