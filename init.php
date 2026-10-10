<?php
// Kleeja Plugin
// kj_recaptcha
// Version: 2.0
// Developer: Kleeja team

// Prevent illegal run
if (!defined('IN_PLUGINS_SYSTEM')) {
    exit();
}

// Plugin Basic Information
$kleeja_plugin['kj_recaptcha']['information'] = [
    // The casual name of this plugin, anything can a human being understands
    'plugin_title' => [
        'en' => 'reCAPTCHA Protection',
        'ar' => 'الحماية عبر reCAPTCHA',
    ],
    // Who wrote this plugin?
    'plugin_developer' => 'Kleeja.net',
    //settings page, if there is one (what after ? like cp=j_plugins)
    'settings_page' => 'cp=options&smt=kj_recaptcha',
    // This plugin version
    'plugin_version' => '2.0',
    // Explain what is this plugin, why should I use it?
    'plugin_description' => [
        'en' =>
            'Protect the login, registration, password recovery, contact, report and upload forms, and the control panel login, with Google reCAPTCHA v2 or v3 in place of the Kleeja security code.',
        'ar' =>
            'تحمي نماذج الدخول والتسجيل واستعادة كلمة المرور والاتصال والإبلاغ والرفع، وتسجيل الدخول إلى لوحة التحكم، بـ reCAPTCHA من Google (الإصدار 2 أو 3) بدلًا من كود الأمان في كليجا.',
    ],
    // Min version of Kleeja that's requiered to run this plugin
    'plugin_kleeja_version_min' => '4.0.0',
    // Max version of Kleeja that support this plugin, use 0 for unlimited
    'plugin_kleeja_version_max' => '4.9',
    // Should this plugin run before others?, 0 is normal, and higher number has high priority
    'plugin_priority' => 0,
];

//after installation message, you can remove it, it's not requiered
$kleeja_plugin['kj_recaptcha']['first_run']['ar'] = "
تضيف هذه الإضافة تبويب «خيارات reCAPTCHA» إلى صفحة الإعدادات. تبقى كليجا على كود الأمان الخاص بها حتى تختار نوع reCAPTCHA
وتُدخل مفتاح الموقع والمفتاح السري اللذين تحصل عليهما من لوحة إدارة reCAPTCHA في Google.
<br>
<a href='./index.php?cp=options&amp;smt=kj_recaptcha'>خيارات reCAPTCHA</a> -
<a href='./index.php?cp=s_help#help-kj_recaptcha'>دليل الإضافة</a> -
<a href='https://github.com/kleeja/kj_recaptcha/issues' target='_blank' rel='noopener'>الإبلاغ عن مشكلة</a>
";

$kleeja_plugin['kj_recaptcha']['first_run']['en'] = "
This plugin adds a reCAPTCHA settings tab to the Settings page. Kleeja keeps its own security code until you choose
a reCAPTCHA type and enter the site key and the secret key that you get from the Google reCAPTCHA admin console.
<br>
<a href='./index.php?cp=options&amp;smt=kj_recaptcha'>reCAPTCHA settings</a> -
<a href='./index.php?cp=s_help#help-kj_recaptcha'>Plugin guide</a> -
<a href='https://github.com/kleeja/kj_recaptcha/issues' target='_blank' rel='noopener'>Report a problem</a>
";

// Plugin Installation function
$kleeja_plugin['kj_recaptcha']['install'] = function ($plg_id) {
    $options = [];

    foreach (kj_recaptcha_configs() as $name => $setting) {
        $options[$name] = [
            'value' => $setting['value'],
            'html' => $setting['html'],
            'order' => $setting['order'],
            'plg_id' => $plg_id,
            'type' => 'kj_recaptcha',
        ];
    }

    add_config_r($options);

    foreach (kj_recaptcha_translations() as $lang_id => $words) {
        add_olang($words, $lang_id, $plg_id);
    }
};

// the words and the fields of the settings are written again, so a new version brings its own (2.0 rewrote all of them),
// with queries of its own: add_olang(), delete_olang(), add_config(), delete_config() and delete_cache() run hooks,
// or need the groups, and the plugins are still loading.
// $kleeja_plugin isn't a global, Kleeja includes this file in a method, so the closure takes the information above with it
$kleeja_plugin['kj_recaptcha']['update'] = function ($old_version, $new_version) use ($kleeja_plugin) {
    global $SQL, $dbprefix;

    $result = $SQL->build([
        'SELECT' => 'plg_id',
        'FROM' => "{$dbprefix}plugins",
        'WHERE' => 'plg_name = :name',
        'BIND' => ['name' => 'kj_recaptcha'],
    ]);

    $row = $SQL->fetch_array($result);
    $plg_id = (int) ($row['plg_id'] ?? 0);
    $SQL->freeresult($result);

    //a plg_id of 0 would delete the words of the core and of every plugin
    if ($plg_id > 0) {
        $SQL->build([
            'DELETE' => "{$dbprefix}lang",
            'WHERE' => 'plg_id = :plg_id',
            'BIND' => ['plg_id' => $plg_id],
        ]);

        foreach (kj_recaptcha_translations() as $lang_id => $words) {
            foreach ($words as $word => $trans) {
                $SQL->build([
                    'INSERT' => 'word, trans, lang_id, plg_id',
                    'INTO' => "{$dbprefix}lang",
                    'VALUES' => ':word, :trans, :lang_id, :plg_id',
                    'BIND' => ['word' => $word, 'trans' => $trans, 'lang_id' => $lang_id, 'plg_id' => $plg_id],
                ]);
            }
        }
    }

    // the saved settings, versions before 1.4 had a yes/no setting for the invisible badge instead of the type
    $saved = [];

    $result = $SQL->build([
        'SELECT' => 'name, value',
        'FROM' => "{$dbprefix}config",
        'WHERE' => 'name IN (:names)',
        'BIND' => ['names' => array_merge(array_keys(kj_recaptcha_configs()), ['kj_recaptcha_invisible'])],
    ]);

    while ($row = $SQL->fetch_array($result)) {
        $saved[$row['name']] = $row['value'];
    }

    $SQL->freeresult($result);

    foreach (kj_recaptcha_configs() as $name => $setting) {
        if (array_key_exists($name, $saved)) {
            // the field of the setting and its place, the value is kept
            $SQL->build([
                'UPDATE' => "{$dbprefix}config",
                'SET' => '`option` = :option, display_order = :display_order',
                'WHERE' => 'name = :name',
                'BIND' => ['option' => $setting['html'], 'display_order' => $setting['order'], 'name' => $name],
            ]);

            continue;
        }

        // the type of a site that runs a version before 1.4, which showed reCAPTCHA v2 as soon as it was installed
        $value = $setting['value'];

        if ($name === 'kj_recaptcha_type' && array_key_exists('kj_recaptcha_invisible', $saved)) {
            $value = (int) $saved['kj_recaptcha_invisible'] === 1 ? '2' : '1';
        }

        $SQL->build([
            'INSERT' => '`name`, `value`, `option`, `display_order`, `type`, `plg_id`, `dynamic`',
            'INTO' => "{$dbprefix}config",
            'VALUES' => ':name, :value, :option, :display_order, :type, :plg_id, :dynamic',
            'BIND' => [
                'name' => $name,
                'value' => $value,
                'option' => $setting['html'],
                'display_order' => $setting['order'],
                'type' => 'kj_recaptcha',
                'plg_id' => $plg_id,
                'dynamic' => 0,
            ],
        ]);
    }

    if (array_key_exists('kj_recaptcha_invisible', $saved)) {
        $SQL->build([
            'DELETE' => "{$dbprefix}config",
            'WHERE' => 'name = :name',
            'BIND' => ['name' => 'kj_recaptcha_invisible'],
        ]);
    }

    // the developer and the description that the old versions were installed with,
    // the list of plugins shows them while the plugin is disabled
    if (version_compare($old_version, '2.0', '<')) {
        $SQL->build([
            'UPDATE' => "{$dbprefix}plugins",
            'SET' => 'plg_author = :author, plg_dsc = :description',
            'WHERE' => 'plg_name = :name',
            'BIND' => [
                'author' => 'Kleeja.net',
                'description' => kleeja_html_encode(
                    $kleeja_plugin['kj_recaptcha']['information']['plugin_description']['en'],
                ),
                'name' => 'kj_recaptcha',
            ],
        ]);
    }

    // the cached words and settings, and the compiled templates of the forms: versions before 2.0 wrote the type
    // and the site key in them. They are PHP files, and OPcache would keep running the old ones for a while,
    // or until PHP restarts when it doesn't check the files. Before a file is deleted,
    // OPcache finds a relative path only when the file exists
    $cached = array_merge(glob(PATH . 'cache/data_lang*.php') ?: [], [PATH . 'cache/data_config.php']);
    $templates = implode('|', array_map('preg_quote', array_keys(kj_recaptcha_templates())));

    foreach (glob(PATH . 'cache/tpl_*.php') ?: [] as $file) {
        if (preg_match('/^tpl_(' . $templates . ')([0-9a-f]{32})?\.php$/', basename($file))) {
            $cached[] = $file;
        }
    }

    foreach ($cached as $file) {
        if (file_exists($file)) {
            if (function_exists('opcache_invalidate')) {
                @opcache_invalidate($file, true);
            }

            @unlink($file);
        }
    }
};

// Plugin Uninstallation, function to be called at unistalling
$kleeja_plugin['kj_recaptcha']['uninstall'] = function ($plg_id) {
    delete_config(array_keys(kj_recaptcha_configs()));

    //a plg_id of 0 would delete the words of the core and of every plugin
    if ((int) $plg_id > 0) {
        foreach (array_keys(kj_recaptcha_translations()) as $lang_id) {
            delete_olang(null, $lang_id, (int) $plg_id);
        }
    }
};

// Plugin functions
$kleeja_plugin['kj_recaptcha']['functions'] = [
    //the words of the plugin on the settings page, in English for a language that has no translation of them
    'begin_admin_page' => function ($args) {
        $olang = (array) ($args['olang'] ?? []) + kj_recaptcha_translations()['en'];

        return compact('olang');
    },

    // the forms where Kleeja would ask for its security code get the reCAPTCHA instead: the login, registration,
    // password recovery, contact and report forms with "Enable Captcha", the upload form with the safety code
    // for uploads. The login of the control panel is in before_display_template_admin_page
    'end_common' => function ($args) {
        global $config, $tpl, $lang;

        if (defined('IN_ADMIN') || !kj_recaptcha_type()) {
            return;
        }

        $fields = [];

        if (!empty($config['enable_captcha'])) {
            define('KJ_RECAPTCHA_FORMS', true);

            foreach (kj_recaptcha_templates() as [$action]) {
                if (!in_array($action, ['upload', 'admin_login'])) {
                    $fields[$action] = kj_recaptcha_field($action);
                }
            }
        }

        if (!empty($config['safe_code'])) {
            define('KJ_RECAPTCHA_UPLOAD', true);
            $fields['upload'] = kj_recaptcha_field('upload');

            // the uploader says that the security code is wrong when the reCAPTCHA is not verified
            if (defined('IN_REAL_INDEX')) {
                $lang['WRONG_VERTY_CODE'] = kj_recaptcha_word('KJ_RECAPTCHA_FAILED');
            }
        }

        $config['enable_captcha'] = 0;
        $config['safe_code'] = 0;

        $tpl->assign('kj_recaptcha_field', $fields);
    },

    // the place of the reCAPTCHA in the templates, before the security code of Kleeja. The template only prints
    // what end_common gives it, so a change of the settings shows at once, without compiling the templates again
    'style_parse_func' => function ($args) {
        $template = (string) ($args['template_name'] ?? '');
        $templates = kj_recaptcha_templates();

        if (!isset($templates[$template])) {
            return;
        }

        [$action, $place] = $templates[$template];

        $html = preg_replace(
            $place,
            '<IF ISSET="kj_recaptcha_field.' . $action . '">{kj_recaptcha_field.' . $action . '}</IF>' . "\n" . '$0',
            $args['html'],
            1,
        );

        return compact('html');
    },

    // the login of the control panel always asks for the reCAPTCHA, instead of the code that Kleeja asks for
    // after a wrong password
    'before_display_template_admin_page' => function ($args) {
        global $tpl;

        if (!kj_recaptcha_admin()) {
            return;
        }

        $show_captcha = false;
        $tpl->assign('kj_recaptcha_field', ['admin_login' => kj_recaptcha_field('admin_login')]);

        return compact('show_captcha');
    },

    'admin_login_submit' => function ($args) {
        if (!kj_recaptcha_admin()) {
            return;
        }

        return kj_recaptcha_errors($args['ERRORS'], 'admin_login');
    },

    'login_after_submit' => function ($args) {
        return defined('KJ_RECAPTCHA_FORMS') ? kj_recaptcha_errors($args['ERRORS'], 'login') : null;
    },

    'register_submit' => function ($args) {
        return defined('KJ_RECAPTCHA_FORMS') ? kj_recaptcha_errors($args['ERRORS'], 'register') : null;
    },

    // the security code of this form goes with the others, the form needs the reCAPTCHA as well
    'submit_get_pass' => function ($args) {
        return defined('KJ_RECAPTCHA_FORMS') ? kj_recaptcha_errors($args['ERRORS'], 'get_pass') : null;
    },

    'submit_call_go_page' => function ($args) {
        return defined('KJ_RECAPTCHA_FORMS') ? kj_recaptcha_errors($args['ERRORS'], 'call') : null;
    },

    'submit_report_go_page' => function ($args) {
        return defined('KJ_RECAPTCHA_FORMS') ? kj_recaptcha_errors($args['ERRORS'], 'report') : null;
    },

    // the uploader checks the code with kleeja_check_captcha(), which asks Google below
    'defaultUploader_upload_1st' => function ($args) {
        if (!defined('KJ_RECAPTCHA_UPLOAD')) {
            return;
        }

        $captcha_enabled = true;

        return compact('captcha_enabled');
    },

    // the hook of the FTP uploader of older versions, kept for an uploader that a plugin still brings
    'ftpUploader_upload_1st' => function ($args) {
        if (!defined('KJ_RECAPTCHA_UPLOAD')) {
            return;
        }

        $captcha_enabled = true;

        return compact('captcha_enabled');
    },

    'kleeja_check_captcha_func' => function ($args) {
        // the upload form, its code is the reCAPTCHA
        if (defined('IN_REAL_INDEX') && defined('KJ_RECAPTCHA_UPLOAD')) {
            $return = kj_recaptcha_verify('upload');

            return compact('return');
        }

        // the login of the control panel, its reCAPTCHA is checked in admin_login_submit
        if (defined('IN_ADMIN') && kj_recaptcha_admin()) {
            $return = true;

            return compact('return');
        }
    },

    // the guide of the plugin on the help page of the control panel, its words are in language/help_{code}.php
    'admin_help_guides' => function ($args) {
        $help_guides = $args['help_guides'];
        $words = kj_recaptcha_help_words();

        // the name of the plugin is the key, so Kleeja knows that the plugin has its guide, and shows its icon.
        // The options of the plugin are a tab of the settings page, whose help button opens the guide of Kleeja,
        // so it has no 'page', and the guide of the settings links to it
        $help_guides['kj_recaptcha'] = [
            'group' => 'plugins',
            'title' => $words['KJ_RECAPTCHA_HELP_TITLE'],
            'intro' => $words['KJ_RECAPTCHA_HELP_INTRO'],
            'link' => './?cp=options&amp;smt=kj_recaptcha',
            // tips and warnings are shown beside the others on wide screens
            'sections' => [
                kj_recaptcha_help_section($words, 'features', 'FEATURE'),
                kj_recaptcha_help_section($words, 'steps', 'STEP'),
                kj_recaptcha_help_section($words, 'faq', 'FAQ'),
                kj_recaptcha_help_section($words, 'tips', 'TIP'),
                kj_recaptcha_help_section($words, 'warnings', 'WARNING'),
            ],
        ];

        if (isset($help_guides['settings'])) {
            $help_guides['settings']['sections'][] = ['type' => 'text', 'text' => $words['KJ_RECAPTCHA_HELP_NOTE']];
        }

        return compact('help_guides');
    },
];

/**
 * special functions
 */

if (!function_exists('kj_recaptcha_configs')) {
    // the version of the script of the plugin, a new one is downloaded again by the browsers
    define('KJ_RECAPTCHA_VERSION', '2.0');
    // the address where the server verifies an answer, with the secret key
    define('KJ_RECAPTCHA_VERIFY_URL', 'https://www.google.com/recaptcha/api/siteverify');
    // the lowest score of v3 that passes, from 0.0 (a bot) to 1.0 (a human), Google suggests 0.5 to begin with
    define('KJ_RECAPTCHA_MIN_SCORE', 0.5);

    /**
     * the settings of the plugin, with the values that it is installed with, their order,
     * and their fields on the settings page, which adds the classes of Bootstrap to them
     * @return array
     */
    function kj_recaptcha_configs()
    {
        // the values of the type, as kj_recaptcha_type() reads them
        $names = [
            '0' => 'KJ_RECAPTCHA_DISABLED',
            '1' => 'KJ_RECAPTCHA_DEFAULT',
            '2' => 'KJ_RECAPTCHA_INVISIBLE',
            '3' => 'KJ_RECAPTCHA_VERSION3',
        ];
        $types = '';

        foreach ($names as $value => $word) {
            $types .=
                '<option value="' .
                $value .
                '"<IF NAME="con.kj_recaptcha_type==' .
                $value .
                '"> selected="selected"</IF>>{olang.' .
                $word .
                '}</option>';
        }

        // a key is written from left to right, and the browser shouldn't fill it or check its spelling
        $key = function ($name) {
            return '<input type="text" id="' .
                $name .
                '" name="' .
                $name .
                '" value="{con.' .
                $name .
                '}" dir="ltr" autocomplete="off" spellcheck="false" />';
        };

        return [
            'kj_recaptcha_type' => [
                'value' => '0',
                'order' => 1,
                'html' => '<select id="kj_recaptcha_type" name="kj_recaptcha_type">' . $types . '</select>',
            ],
            'kj_recaptcha_sitekey' => [
                'value' => '',
                'order' => 2,
                'html' => $key('kj_recaptcha_sitekey'),
            ],
            'kj_recaptcha_secret' => [
                'value' => '',
                'order' => 3,
                'html' => $key('kj_recaptcha_secret'),
            ],
        ];
    }

    /**
     * the templates that get the reCAPTCHA: the name of the form, which v3 sends with every answer and the server checks,
     * and the security code of Kleeja that the reCAPTCHA is placed before
     * @return array
     */
    function kj_recaptcha_templates()
    {
        $forms = '/<IF\s+NAME\s*=\s*"config\.enable_captcha\s*==\s*1"\s*>/i';

        return [
            'login' => ['login', $forms],
            'register' => ['register', $forms],
            'get_pass' => ['get_pass', $forms],
            'call' => ['call', $forms],
            'report' => ['report', $forms],
            'index_body' => ['upload', '/<IF\s+NAME\s*=\s*"config\.safe_code"\s*>/i'],
            'admin_login' => ['admin_login', '/<IF\s+NAME\s*=\s*"show_captcha"\s*>/i'],
        ];
    }

    /**
     * a key of the settings as it was typed
     * @param  string $name sitekey or secret
     * @return string
     */
    function kj_recaptcha_key($name)
    {
        global $config;

        return trim(htmlspecialchars_decode((string) ($config['kj_recaptcha_' . $name] ?? ''), ENT_QUOTES));
    }

    /**
     * the type of reCAPTCHA in use: 1 the "I'm not a robot" checkbox, 2 the invisible badge, 3 v3,
     * or 0 while it is disabled or a key is missing, Kleeja keeps its own security code then
     * @return int
     */
    function kj_recaptcha_type()
    {
        global $config;

        $type = (int) ($config['kj_recaptcha_type'] ?? 0);

        if (
            !in_array($type, [1, 2, 3], true) ||
            kj_recaptcha_key('sitekey') === '' ||
            kj_recaptcha_key('secret') === ''
        ) {
            return 0;
        }

        return $type;
    }

    /**
     * the login of the control panel asks for the reCAPTCHA, unless config.php stops the captchas with STOP_CAPTCHA
     * @return bool
     */
    function kj_recaptcha_admin()
    {
        return defined('IN_ADMIN') && !defined('STOP_CAPTCHA') && kj_recaptcha_type() > 0;
    }

    /**
     * a word of the plugin in the language of the page, or in English
     * @param  string $word
     * @return string
     */
    function kj_recaptcha_word($word)
    {
        global $olang;

        return $olang[$word] ?? kj_recaptcha_translations()['en'][$word];
    }

    /**
     * the reCAPTCHA of a form, the script of the plugin shows it
     * @param  string $action the name of the form
     * @return string
     */
    function kj_recaptcha_field($action)
    {
        global $config;

        $type = kj_recaptcha_type();
        $language = preg_replace('/[^a-z_-]/i', '', (string) ($config['language'] ?? ''));
        $script =
            $config['siteurl'] .
            KLEEJA_PLUGINS_FOLDER .
            '/kj_recaptcha/assets/kj_recaptcha.js?v=' .
            KJ_RECAPTCHA_VERSION;

        // v3 shows nothing in the form, its badge is at the corner of the page
        return '<div class="kj-recaptcha"' .
            ($type === 3 ? ' hidden' : ' style="margin: 1rem 0"') .
            ' data-type="' .
            $type .
            '" data-sitekey="' .
            htmlspecialchars(kj_recaptcha_key('sitekey'), ENT_QUOTES, 'UTF-8') .
            '" data-hl="' .
            $language .
            '" data-action="' .
            $action .
            '"></div>' .
            "\n" .
            '<script src="' .
            htmlspecialchars($script, ENT_QUOTES, 'UTF-8') .
            '" defer></script>';
    }

    /**
     * the errors of a form, with one more when its reCAPTCHA is not verified
     * @param  array|bool $errors
     * @param  string     $action the name of the form
     * @return array|null
     */
    function kj_recaptcha_errors($errors, $action)
    {
        if (kj_recaptcha_verify($action)) {
            return null;
        }

        $ERRORS = (array) $errors;
        $ERRORS['recaptcha'] = kj_recaptcha_word('KJ_RECAPTCHA_FAILED');

        // the login of the control panel ends every error with a full stop of its own
        if ($action === 'admin_login') {
            $ERRORS['recaptcha'] = rtrim($ERRORS['recaptcha'], '.');
        }

        return compact('ERRORS');
    }

    /**
     * verify the answer of the form with Google. An answer can be verified only once,
     * so the result is kept for the rest of the request
     * @param  string $action the name of the form, v3 sends it with the answer
     * @return bool
     */
    function kj_recaptcha_verify($action)
    {
        static $results = [];

        if (isset($results[$action])) {
            return $results[$action];
        }

        $type = kj_recaptcha_type();

        if (!$type) {
            return $results[$action] = true;
        }

        $token = $_POST['g-recaptcha-response'] ?? '';

        if (!is_string($token) || trim($token) === '' || strlen($token) > 8192) {
            return $results[$action] = false;
        }

        $answer = kj_recaptcha_request([
            'secret' => kj_recaptcha_key('secret'),
            'response' => trim($token),
            'remoteip' => get_ip(),
        ]);

        if (empty($answer['success'])) {
            kleeja_log(
                '[kj_recaptcha] ' .
                    $action .
                    ' not verified: ' .
                    ($answer === null
                        ? 'no answer from Google'
                        : implode(', ', (array) ($answer['error-codes'] ?? []))),
            );

            return $results[$action] = false;
        }

        // v3 gives a score instead of a challenge, and the name of the form that asked for the answer
        if ($type === 3) {
            if (!isset($answer['score']) || (float) $answer['score'] < KJ_RECAPTCHA_MIN_SCORE) {
                kleeja_log('[kj_recaptcha] ' . $action . ' score too low: ' . ($answer['score'] ?? 'none'));

                return $results[$action] = false;
            }

            if (isset($answer['action']) && $answer['action'] !== $action) {
                kleeja_log('[kj_recaptcha] ' . $action . ' got the answer of ' . $answer['action']);

                return $results[$action] = false;
            }
        }

        return $results[$action] = true;
    }

    /**
     * ask Google about an answer, the secret key is sent in a POST request over a verified HTTPS connection
     * @param  array      $fields
     * @return array|null the answer of Google, or null when it can't be reached
     */
    function kj_recaptcha_request($fields)
    {
        $body = http_build_query($fields, '', '&');
        $response = false;

        if (function_exists('curl_init')) {
            $curl = curl_init(KJ_RECAPTCHA_VERIFY_URL);

            curl_setopt_array($curl, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT => 10,
            ]);

            $response = curl_exec($curl);
        } elseif (filter_var(ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOLEAN)) {
            $response = @file_get_contents(
                KJ_RECAPTCHA_VERIFY_URL,
                false,
                stream_context_create([
                    'http' => [
                        'method' => 'POST',
                        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                        'content' => $body,
                        'timeout' => 10,
                    ],
                ]),
            );
        }

        $answer = is_string($response) ? json_decode($response, true) : null;

        return is_array($answer) ? $answer : null;
    }

    /**
     * the words of the guide on the help page, in the language of the admin,
     * a word that the translation misses is shown in English
     * @return array
     */
    function kj_recaptcha_help_words()
    {
        global $config;

        $words = (array) require __DIR__ . '/language/help_en.php';
        $language = preg_replace('/[^a-z0-9_-]/i', '', (string) ($config['language'] ?? ''));
        $translation = __DIR__ . "/language/help_{$language}.php";

        if ($language !== '' && $language !== 'en' && file_exists($translation)) {
            // in its own line, Prettier drops the brackets of (require $translation) + $words
            $translated = require $translation;
            $words = (array) $translated + $words;
        }

        return $words;
    }

    /**
     * a section of the guide from its numbered words, like KJ_RECAPTCHA_HELP_TIP_1, KJ_RECAPTCHA_HELP_TIP_2 ..
     * its title, when it has its own, is KJ_RECAPTCHA_HELP_TIP_TITLE
     * @param  array  $words
     * @param  string $type  how Kleeja shows it: features, steps, tips, warnings or faq
     * @param  string $name  the name of its words, KJ_RECAPTCHA_HELP_{name}_1
     * @return array
     */
    function kj_recaptcha_help_section($words, $type, $name)
    {
        $prefix = 'KJ_RECAPTCHA_HELP_' . $name;
        $section = ['type' => $type, 'title' => $words[$prefix . '_TITLE'] ?? '', 'items' => []];

        for ($n = 1; isset($words[$prefix . ($type === 'faq' ? '_Q_' : '_') . $n]); $n++) {
            $section['items'][] =
                $type === 'faq'
                    ? ['q' => $words[$prefix . '_Q_' . $n], 'a' => $words[$prefix . '_A_' . $n] ?? '']
                    : $words[$prefix . '_' . $n];
        }

        return $section;
    }

    /**
     * the words of the plugin, they are added to the language table when it is installed or updated,
     * a <small> in the name of a setting is the hint under it. A word is 255 letters at most, tags included,
     * the length of the trans column on MySQL
     * @return array
     */
    function kj_recaptcha_translations()
    {
        return [
            'en' => [
                'CONFIG_KLJ_MENUS_KJ_RECAPTCHA' => 'reCAPTCHA settings',
                'KJ_RECAPTCHA_TYPE' =>
                    'reCAPTCHA type <small>Choose the type of your keys. reCAPTCHA replaces the security code on every form where that code is enabled, and protects the control panel login. Until both keys are filled in, Kleeja keeps its own code.</small>',
                'KJ_RECAPTCHA_SITEKEY' =>
                    'Site key <small>The public key from the reCAPTCHA admin console. Visitors\' browsers use it to show reCAPTCHA.</small>',
                'KJ_RECAPTCHA_SECRET' =>
                    'Secret key <small>The private key from the reCAPTCHA admin console. Your server uses it to verify each answer with Google. Keep it private.</small>',
                'KJ_RECAPTCHA_DISABLED' => 'Disabled (use the Kleeja security code)',
                'KJ_RECAPTCHA_DEFAULT' => 'v2: “I\'m not a robot” checkbox',
                'KJ_RECAPTCHA_INVISIBLE' => 'v2: Invisible badge',
                'KJ_RECAPTCHA_VERSION3' => 'v3: Background score, no challenge',
                'KJ_RECAPTCHA_FAILED' => 'The reCAPTCHA verification failed. Please try again.',
            ],
            'ar' => [
                'CONFIG_KLJ_MENUS_KJ_RECAPTCHA' => 'خيارات reCAPTCHA',
                'KJ_RECAPTCHA_TYPE' =>
                    'نوع reCAPTCHA <small>اختر نوع مفاتيحك. يحلّ reCAPTCHA محل كود الأمان في كل نموذج فُعِّل فيه هذا الكود، ويحمي تسجيل الدخول إلى لوحة التحكم. وتُبقي كليجا على كودها الخاص حتى تُدخل المفتاحين.</small>',
                'KJ_RECAPTCHA_SITEKEY' =>
                    'مفتاح الموقع <small>المفتاح العام من لوحة إدارة reCAPTCHA، تستخدمه متصفحات الزوار لعرض reCAPTCHA.</small>',
                'KJ_RECAPTCHA_SECRET' =>
                    'المفتاح السري <small>المفتاح الخاص من لوحة إدارة reCAPTCHA، يستخدمه خادمك للتحقق من كل إجابة لدى Google. احتفظ به سرًّا.</small>',
                'KJ_RECAPTCHA_DISABLED' => 'معطّل (استخدام كود الأمان في كليجا)',
                'KJ_RECAPTCHA_DEFAULT' => 'الإصدار 2: مربع «أنا لست برنامج روبوت»',
                'KJ_RECAPTCHA_INVISIBLE' => 'الإصدار 2: شارة غير مرئية',
                'KJ_RECAPTCHA_VERSION3' => 'الإصدار 3: تقييم في الخلفية دون اختبار',
                'KJ_RECAPTCHA_FAILED' => 'تعذّر التحقق عبر reCAPTCHA، يُرجى المحاولة مرة أخرى.',
            ],
        ];
    }
}
