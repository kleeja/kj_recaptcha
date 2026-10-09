<?php
//
// kj_recaptcha, its guide on the help page of the control panel
// Arabic, see help_en.php for how the words are named
//
// A word missing here is shown in English. Names in <strong> are written as the control panel shows them in Arabic,
// the words of Kleeja are in lang/ar/, the words of the plugin in kj_recaptcha_translations() of init.php.
// A <code> that ends with a symbol takes dir="ltr", or the symbol shows on the wrong side.
//

return [
    'KJ_RECAPTCHA_HELP_TITLE' => 'الحماية عبر reCAPTCHA',
    'KJ_RECAPTCHA_HELP_INTRO' =>
        'أوقف الروبوتات والرسائل المزعجة بـ reCAPTCHA من Google. تحلّ الإضافة محل كود الأمان في نماذج موقعك وتحمي تسجيل الدخول إلى لوحة التحكم، بمربع الإصدار 2 أو بشارته غير المرئية أو بالإصدار 3.',

    //
    // what the plugin does
    //
    'KJ_RECAPTCHA_HELP_FEATURE_1' =>
        'عند تفعيل <strong>تفعيل كود الامان بكليجا</strong>، تطلب نماذج الدخول والتسجيل واستعادة كلمة المرور و<strong>اتصل بنا</strong> و<strong>إبلاغ عن محتوى</strong> اجتياز reCAPTCHA بدلًا من كود الأمان.',
    'KJ_RECAPTCHA_HELP_FEATURE_2' =>
        'عند تفعيل <strong>تفعيل الكود الأمني في التحميل</strong>، يطلب نموذج الرفع اجتياز reCAPTCHA قبل قبول الملفات.',
    'KJ_RECAPTCHA_HELP_FEATURE_3' =>
        'يطلب تسجيل الدخول إلى لوحة التحكم اجتياز reCAPTCHA دائمًا، بدلًا من الكود الذي تعرضه كليجا بعد إدخال كلمة مرور خاطئة.',
    'KJ_RECAPTCHA_HELP_FEATURE_4' =>
        'ثلاثة أنواع: <strong>مربع «أنا لست برنامج روبوت»</strong>، و<strong>الشارة غير المرئية</strong> التي لا تعرض اختبارًا إلا إذا اشتبهت Google في أن الزائر روبوت، و<strong>الإصدار 3</strong> الذي لا يعرض أي اختبار ويرفض الطلبات التي يقل تقييمها عن 0.5.',
    'KJ_RECAPTCHA_HELP_FEATURE_5' =>
        'يتحقق خادمك من كل إجابة لدى Google بمفتاحك السري، فلا يستطيع الروبوت تجاوز التحقق بإرسال النموذج مباشرة.',
    'KJ_RECAPTCHA_HELP_FEATURE_6' =>
        'يظهر reCAPTCHA بلغة الصفحة، ويتبع المربع الوضع الداكن في ستايل bootstrap وفي لوحة التحكم.',

    //
    // how to use it
    //
    'KJ_RECAPTCHA_HELP_STEP_TITLE' => 'إعداد reCAPTCHA',
    'KJ_RECAPTCHA_HELP_STEP_1' =>
        'افتح <a href="https://www.google.com/recaptcha/admin/create" target="_blank" rel="noopener">لوحة إدارة reCAPTCHA</a> وسجّل الدخول بحسابك في Google.',
    'KJ_RECAPTCHA_HELP_STEP_2' =>
        'اكتب اسمًا للمفتاح، واختر نوعه: الإصدار 3 القائم على التقييم، أو الإصدار 2 القائم على الاختبار مع مربع «أنا لست برنامج روبوت» أو شارة reCAPTCHA غير المرئية.',
    'KJ_RECAPTCHA_HELP_STEP_3' =>
        'أضف نطاق موقعك، مثل <code>example.com</code>، دون <code dir="ltr">https://</code> ودون أي مسار. تحفظ Google كل مفتاح في مشروع على Google Cloud، فاختر مشروعًا أو دع Google تُنشئه.',
    'KJ_RECAPTCHA_HELP_STEP_4' =>
        'أرسل النموذج، ثم انسخ <strong>مفتاح الموقع</strong> و<strong>المفتاح السري</strong>.',
    'KJ_RECAPTCHA_HELP_STEP_5' =>
        'في كليجا، افتح <strong>إعدادات</strong> ثم <strong>خيارات reCAPTCHA</strong>. اختر في <strong>نوع reCAPTCHA</strong> النوع المطابق لمفاتيحك، والصق <strong>مفتاح الموقع</strong> و<strong>المفتاح السري</strong>، ثم اضغط <strong>تحديث الإعدادات</strong>.',
    'KJ_RECAPTCHA_HELP_STEP_6' =>
        'تأكد من تفعيل <strong>تفعيل كود الامان بكليجا</strong> (في <strong>خيارات الواجهة والتصميم</strong>) و<strong>تفعيل الكود الأمني في التحميل</strong> (في <strong>اعدادات التحميل</strong>) للنماذج التي تريد حمايتها.',
    'KJ_RECAPTCHA_HELP_STEP_7' =>
        'افتح صفحة الدخول في موقعك في نافذة خاصة، وسجّل الدخول لتتأكد من أن reCAPTCHA يعمل قبل أن تخرج من لوحة التحكم.',

    //
    // common questions
    //
    'KJ_RECAPTCHA_HELP_FAQ_Q_1' => 'أي نوع أختار؟',
    'KJ_RECAPTCHA_HELP_FAQ_A_1' =>
        'المربع هو الأكثر ألفة لدى الزوار، ويستطيع كل زائر اجتيازه بحل الاختبار. والشارة غير المرئية تُبقي النموذج نظيفًا، ولا تعرض اختبارًا إلا عندما لا تكون Google متأكدة. أما الإصدار 3 فلا يقاطع الزوار أبدًا، لكن الزائر ذا التقييم المنخفض لا يجد اختبارًا يحله، فلا يستطيع إرسال النموذج إطلاقًا. إذا رُفض زوار حقيقيون مع الإصدار 3، فانتقل إلى أحد نوعي الإصدار 2.',
    'KJ_RECAPTCHA_HELP_FAQ_Q_2' => 'تظهر رسالة فشل التحقق عبر reCAPTCHA في كل نموذج، لماذا؟',
    'KJ_RECAPTCHA_HELP_FAQ_A_2' =>
        'تأكد من أن <strong>نوع reCAPTCHA</strong> يطابق نوع المفاتيح (مفتاح الإصدار 3 لا يعمل مع الإصدار 2، ومفتاح المربع لا يعمل مع الشارة غير المرئية)، ومن أن نطاق موقعك موجود في قائمة نطاقات المفتاح، ومن أن خادمك يستطيع الوصول إلى <code>www.google.com</code> عبر HTTPS. وعند إضافة <code>DEV_STAGE</code> إلى <code>config.php</code>، تكتب كليجا السبب الذي تذكره Google في <code>cache/kleeja_log.log</code>.',
    'KJ_RECAPTCHA_HELP_FAQ_Q_3' => 'لماذا لا يظهر reCAPTCHA في نموذج اتصل بنا؟',
    'KJ_RECAPTCHA_HELP_FAQ_A_3' =>
        'يظهر reCAPTCHA فقط حيث تطلب كليجا كود الأمان. فعّل <strong>تفعيل كود الامان بكليجا</strong> لنماذج الدخول والتسجيل واستعادة كلمة المرور و<strong>اتصل بنا</strong> و<strong>إبلاغ عن محتوى</strong>، و<strong>تفعيل الكود الأمني في التحميل</strong> لنموذج الرفع.',
    'KJ_RECAPTCHA_HELP_FAQ_Q_4' => 'هل تفرض Google رسومًا على reCAPTCHA؟',
    'KJ_RECAPTCHA_HELP_FAQ_A_4' =>
        'تحفظ Google مفاتيح reCAPTCHA في مشاريع على Google Cloud، ومعها 10,000 عملية تحقق مجانية كل شهر. وكل عملية دخول أو تسجيل أو رسالة أو رفع هي عملية تحقق واحدة. إذا احتاج موقعك إلى أكثر من ذلك، فراجع صفحة reCAPTCHA في مشروعك على Google Cloud console.',
    'KJ_RECAPTCHA_HELP_FAQ_Q_5' => 'لا أستطيع تسجيل الدخول إلى لوحة التحكم، ماذا أفعل؟',
    'KJ_RECAPTCHA_HELP_FAQ_A_5' =>
        'أضف <code dir="ltr">define(\'STOP_CAPTCHA\', true);</code> إلى <code>config.php</code>، فيتخطى تسجيل الدخول إلى لوحة التحكم reCAPTCHA. سجّل الدخول، وصحّح المفاتيح أو اختر <strong>معطّل</strong> في نوع reCAPTCHA، ثم احذف السطر.',

    //
    // beside the guide
    //
    'KJ_RECAPTCHA_HELP_TIP_1' =>
        'لتجربة reCAPTCHA على جهازك، أضف <code>localhost</code> إلى قائمة نطاقات مفتاح منفصل، واحتفظ بمفاتيحك الحقيقية لموقعك.',
    'KJ_RECAPTCHA_HELP_TIP_2' =>
        'مع الإصدار 3، تعرض صفحة reCAPTCHA في مشروعك تقييمات كل نموذج بالأسماء <code>login</code> و<code>register</code> و<code>get_pass</code> و<code>call</code> و<code>report</code> و<code>upload</code> و<code>admin_login</code>.',
    'KJ_RECAPTCHA_HELP_TIP_3' =>
        'تُبقي كليجا على كود الأمان الخاص بها حتى تُدخل المفتاحين وتختار نوعًا، فلا تبقى نماذجك دون حماية أبدًا.',

    'KJ_RECAPTCHA_HELP_WARNING_1' =>
        'احتفظ بالمفتاح السري سرًّا. إذا تسرّب، فاحذف المفتاح من لوحة إدارة reCAPTCHA وأنشئ مفتاحًا جديدًا.',
    'KJ_RECAPTCHA_HELP_WARNING_2' =>
        'إذا لم يستطع خادمك الوصول إلى Google، فسيُرفض كل نموذج محمي، ومنه تسجيل الدخول إلى لوحة التحكم.',
    'KJ_RECAPTCHA_HELP_WARNING_3' =>
        'يحمّل الزوار سكربت reCAPTCHA من Google في النماذج المحمية. اذكر reCAPTCHA في سياسة الخصوصية لموقعك، وأبقِ شارة reCAPTCHA ظاهرة كما تشترط شروط Google.',
    'KJ_RECAPTCHA_HELP_WARNING_4' =>
        'يرفع ستايل DragDrop الملفات دون إجابة reCAPTCHA. مع هذا الستايل، أبقِ <strong>تفعيل الكود الأمني في التحميل</strong> معطّلًا، وإلا فستُرفض عمليات الرفع.',

    //
    // in the guide of the settings, whose page has the tab of the plugin
    //
    'KJ_RECAPTCHA_HELP_NOTE' =>
        'تبويب <strong>خيارات reCAPTCHA</strong> تابع لإضافة <strong>الحماية عبر reCAPTCHA</strong>. <a href="#help-kj_recaptcha">اقرأ دليلها</a>.',
];
