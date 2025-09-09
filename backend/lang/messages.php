<?php

return [
    'post' => [
        'deleted' => 'Post has been deleted.',
    ],
    'comment' => [
        'deleted' => 'Comment has been deleted.',
    ],
    'validation' => [
        'per_page_in'   => 'Per page must be one of: 5, 10, 20, 50.',
        'title_required'=> 'Title is required.',
        'body_required' => 'Body is required.',
        'slug_unique'   => 'Slug has already been taken.',
        'comment_required'       => 'Comment body is required.',
        'guest_name_required'    => 'Guest name is required for unauthenticated users.',
        'guest_email_required'   => 'Guest email is required for unauthenticated users.',
        'guest_email_email'      => 'Guest email must be a valid email address.',
    ],
];
