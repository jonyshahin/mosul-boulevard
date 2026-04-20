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
];
