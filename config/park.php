<?php

return [
    'booking_url' => env('PARK_BOOKING_URL'),
    'email' => env('PARK_EMAIL', 'info@dolphinbeach.lk'),
    'phone' => env('PARK_PHONE', '+94 77 772 3272'),
    'whatsapp' => env('PARK_WHATSAPP'),
    'footer_address' => env('PARK_FOOTER_ADDRESS', 'Dolphin Beach Resort, Illanthadiya, Alankuda, Kalpitiya, Sri Lanka'),
    'footer_email' => env('PARK_FOOTER_EMAIL', 'info@dolphinbeach.lk'),
    'social_links' => [
        'x' => env('PARK_SOCIAL_X'),
        'linkedin' => env('PARK_SOCIAL_LINKEDIN'),
        'instagram' => env('PARK_SOCIAL_INSTAGRAM'),
    ],
    'source' => 'https://www.makararesorts.lk/dolphin-beach-resort/offers/wilpattu-adventure',
    'customizable_pages' => ['home', 'stay', 'safaris', 'wildlife', 'experiences', 'dining', 'offers', 'gallery', 'contact'],
    'content_images' => [1, 2, 3, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16],
];
