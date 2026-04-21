<?php

return [
    'brand' => 'موصل بوليفارد',
    'greeting' => 'مرحباً :name،',
    'view_request' => 'عرض الطلب',
    'footer' => 'أُرسلت من نظام إدارة مشروع موصل بوليفارد.',

    'assigned' => [
        'subject' => 'تم تعيينك لطلب تفتيش: :title',
        'line_1' => 'تم تعيينك على طلب تفتيش.',
        'line_type' => 'النوع: :type',
        'line_severity' => 'الأولوية: :severity',
        'line_subject' => 'الموضوع: :subject',
        'line_due' => 'تاريخ الاستحقاق: :date',
    ],

    'replied' => [
        'subject' => 'رد جديد على طلب تفتيش: :title',
        'line_1' => 'قام :actor بالرد على طلب تفتيش أنت مشارك فيه.',
        'line_preview' => 'الرد: ":preview"',
    ],

    'transitioned' => [
        'subject' => 'تغيير حالة طلب التفتيش: :title',
        'line_1' => 'قام :actor بنقل الطلب من :from إلى :to.',
        'line_note' => 'ملاحظة: ":note"',
    ],

    'overdue' => [
        'subject' => 'طلب تفتيش متأخر: :title',
        'line_1' => 'تجاوز هذا الطلب تاريخ استحقاقه.',
        'line_due' => 'تاريخ الاستحقاق كان: :date',
    ],

    'additional' => [
        'subject' => 'تنبيه بطلب تفتيش: :title',
        'line_1' => 'يطابق طلب تفتيش قاعدة تنبيه خاصة بك.',
        'line_type' => 'النوع: :type',
        'line_severity' => 'الأولوية: :severity',
    ],

    'nav' => [
        'label' => 'عمليات التفتيش',
    ],

    'pages' => [
        'coming_soon' => 'قريبًا — هذه الصفحة قيد الإنشاء.',

        'index' => [
            'title' => 'طلبات التفتيش',
        ],

        'create' => [
            'title' => 'طلب تفتيش جديد',
        ],

        'show' => [
            'title' => 'طلب التفتيش رقم :id',
        ],

        'edit' => [
            'title' => 'تعديل طلب التفتيش',
        ],
    ],

    'list' => [
        'empty_state' => 'لا توجد طلبات تفتيش مطابقة للفلاتر.',
        'create_via_api_link' => 'إنشاء طلب جديد',
        'overdue_badge' => 'متأخر',
        'replies_count' => ':count ردود',
        'filters' => [
            'status' => 'الحالة',
            'severity' => 'الأولوية',
            'subject_type' => 'الموضوع',
            'subject_all' => 'الكل',
            'subject_villa' => 'فلل',
            'subject_tower_unit' => 'شقق الأبراج',
            'assigned_to_me' => 'المعينة لي',
            'overdue_only' => 'المتأخرة فقط',
            'sort_by' => 'ترتيب حسب',
            'sort_newest' => 'الأحدث أولًا',
            'sort_oldest' => 'الأقدم أولًا',
            'sort_severity' => 'الأولوية من الأعلى إلى الأدنى',
            'sort_due' => 'تاريخ الاستحقاق الأقرب',
            'clear' => 'مسح الفلاتر',
        ],
        'columns' => [
            'id' => 'المعرّف',
            'title' => 'العنوان',
            'subject' => 'الموضوع',
            'type' => 'النوع',
            'severity' => 'الأولوية',
            'status' => 'الحالة',
            'assignee' => 'المسؤول',
            'due_date' => 'الاستحقاق',
            'created' => 'أُنشئ في',
        ],
        'pagination' => [
            'showing' => 'عرض :from إلى :to من :total',
            'previous' => 'السابق',
            'next' => 'التالي',
            'page' => 'صفحة :current من :last',
        ],
    ],

    'detail' => [
        'back' => 'العودة إلى طلبات التفتيش',
        'requester' => 'مقدّم الطلب',
        'assignee' => 'المسؤول',
        'verified_by' => 'تم التحقق بواسطة',
        'created' => 'تاريخ الإنشاء',
        'due_date' => 'تاريخ الاستحقاق',
        'no_due_date' => 'لا يوجد تاريخ استحقاق',
        'location' => 'الموقع',
        'description' => 'الوصف',
        'unassigned' => 'غير مُسنَد',
        'subject' => [
            'label' => 'الموضوع',
            'link_villa' => 'عرض الفيلا',
            'link_tower_unit' => 'عرض شقة البرج',
        ],
        'media_placeholder' => 'معرض الوسائط — قريبًا في 4C (:count مرفقات)',
        'replies' => [
            'timeline_title' => 'الردود',
            'empty' => 'لا توجد ردود بعد.',
            'by' => 'بواسطة :name',
            'status_changed_to' => 'تم تغيير الحالة إلى :status',
            'media_count' => ':count وسائط',
        ],
        'reply_form' => [
            'title' => 'إضافة رد',
            'placeholder' => 'اكتب ردك…',
            'submit_button' => 'إرسال الرد',
            'submitting' => 'جاري الإرسال…',
            'status_change_optional' => 'تغيير الحالة أيضًا (اختياري)',
            'status_change_none' => 'عدم التغيير',
            'success_toast' => 'تم إرسال الرد.',
        ],
        'transitions' => [
            'button' => 'تغيير الحالة',
            'modal_title' => 'تغيير الحالة إلى :status',
            'modal_description' => 'يُحدّث هذا الطلب ويُشاهده مقدم الطلب والمسؤول.',
            'note_label' => 'إضافة ملاحظة (اختياري)',
            'note_placeholder' => 'مثال: تم التحقق من الخلل بعد زيارة الموقع.',
            'confirm_button' => 'تأكيد',
            'cancel_button' => 'إلغاء',
            'success_toast' => 'تم تحديث الحالة.',
            'error_toast' => 'تعذّر تغيير الحالة.',
            'no_options' => 'لا توجد تغييرات حالة متاحة من الحالة الحالية.',
        ],
    ],

    'shared' => [
        'severity' => [
            'low' => 'منخفضة',
            'medium' => 'متوسطة',
            'high' => 'مرتفعة',
            'critical' => 'حرجة',
        ],
        'status' => [
            'open' => 'مفتوح',
            'in_progress' => 'قيد التنفيذ',
            'resolved' => 'تم الحل',
            'verified' => 'تم التحقق',
            'closed' => 'مغلق',
            'reopened' => 'أُعيد فتحه',
        ],
        'category' => [
            'qaqc' => 'ضبط الجودة',
            'safety' => 'السلامة',
            'materials' => 'المواد',
            'other' => 'أخرى',
        ],
        'errors' => [
            'forbidden' => 'ليس لديك الصلاحية لإتمام هذا الإجراء.',
            'not_found' => 'طلب التفتيش غير موجود.',
            'validation_failed' => 'يرجى مراجعة النموذج والمحاولة مجددًا.',
        ],
    ],
];
