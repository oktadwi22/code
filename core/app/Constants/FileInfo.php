<?php

namespace App\Constants;

class FileInfo
{

    /*
    |--------------------------------------------------------------------------
    | File Information
    |--------------------------------------------------------------------------
    |
    | This class basically contain the path of files and size of images.
    | All information are stored as an array. Developer will be able to access
    | this info as method and property using FileManager class.
    |
    */

    public function fileInfo()
    {
        $data['withdrawVerify'] = [
            'path' => 'assets/images/verify/withdraw'
        ];
        $data['depositVerify'] = [
            'path' => 'assets/images/verify/deposit'
        ];
        $data['verify'] = [
            'path' => 'assets/verify'
        ];
        $data['default'] = [
            'path' => 'assets/images/default.png',
        ];
        $data['withdrawMethod'] = [
            'path' => 'assets/images/withdraw/method',
            'size' => '800x800',
        ];
        $data['ticket'] = [
            'path' => 'assets/support',
        ];
        $data['logoIcon'] = [
            'path' => 'assets/images/logoIcon',
        ];
        $data['favicon'] = [
            'size' => '128x128',
        ];
        $data['extensions'] = [
            'path' => 'assets/images/extensions',
            'size' => '36x36',
        ];
        $data['seo'] = [
            'path' => 'assets/images/seo',
            'size' => '1180x600',
        ];
        $data['userProfile'] = [
            'path' => 'assets/images/user/profile',
            'size' => '350x300',
        ];
        $data['adminProfile'] = [
            'path' => 'assets/viseradmin/images/profile',
            'size' => '400x400',
        ];
        $data['reviewerProfile'] = [
            'path' => 'assets/reviewer/images/profile',
            'size' => '400x400',
        ];
        $data['authorThumbnail'] = [
            'path' => 'assets/images/user',
            'size' => '80x80',
        ];
        $data['authorAvatar'] = [
            'path' => 'assets/images/user',
            'size' => '80x80',
        ];
        $data['authorCoverImg'] = [
            'path' => 'assets/images/user',
            'size' => '850x350',
        ];
        $data['category'] = [
            'path' => 'assets/images/category',
            'size' => '140x140',
        ];
        $data['authorLevel'] = [
            'path' => 'assets/images/author_level',
            'size' => '63x64',
        ];

        $data['productInlinePreview'] = [
            'path' => 'assets/files/product',
            'size' => '430x230',
        ];

        $data['productPreview'] = [
            'path' => 'assets/files/product',
            'size' => '860x455',
        ];

        $data['productThumbnail'] = [
            'path' => 'assets/files/product',
            'size' => '80x80',
        ];

        $data['productFile'] = [
            'path' => 'assets/files/product'
        ];

        $data['productCollection'] = [
            'path' => 'assets/images/product/collection',
            'size' => '520x280',
        ];
        $data['screenshotZip'] = [
            'path' => 'assets/files/screenshots_zip'
        ];

        $data['screenshots'] = [
            'path' => 'assets/files/product'
        ];

        $data['contact'] = [
            'path' => 'assets/images/frontend/contact_us'
        ];

        return $data;
    }

}
