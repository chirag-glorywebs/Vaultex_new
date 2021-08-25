<?php
// Aside menu
return [

    'items' => [
        // Dashboard
        [
            'title' => 'Dashboard',
            'root' => true,
            'icon' => 'media/svg/icons/Design/Layers.svg', // or can be 'flaticon-home' or any flaticon-*
            'page' => 'admin/dashboard',
            'new-tab' => false,
        ],
        [
            'title' => 'Users',
            'icon' => 'fa fa-users',
            'bullet' => 'dot',
            'root' => true,
            'submenu' => [
                [
                    'title' => 'Admin Users',
                    'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
                    'bullet' => 'line',
                    'root' => true,
                    'submenu' => [
                        [
                            'title' => 'Add New',
                            'page' => 'admin/adminuser/add'
                        ],
                        [
                            'title' => 'Lists',
                            'page' => 'admin/adminuser/'
                        ],
                    ],
                ],
                [
                    'title' => 'Sales Users',
                    'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
                    'bullet' => 'line',
                    'root' => true,
                    'submenu' => [
                        [
                            'title' => 'Add New',
                            'page' => 'admin/salesuser/add'
                        ],
                        [
                            'title' => 'Lists',
                            'page' => 'admin/salesuser/'
                        ],
                    ],
                ],
                [
                    'title' => 'Vendor Users',
                    'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
                    'bullet' => 'line',
                    'root' => true,
                    'submenu' => [
                        [
                            'title' => 'Add New',
                            'page' => 'admin/vendoruser/add'
                        ],
                        [
                            'title' => 'Lists',
                            'page' => 'admin/vendoruser/'
                        ],
                    ],
                ],
                [
                    'title' => 'Customers',
                    'page' => 'admin/customers',
                    'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg'
                ],
            ],
        ],
        [
            'title' => 'Product Catelog',
            'icon' => 'fa fa-universal-access',
            'bullet' => 'dot',
            'root' => true,
            'submenu' => [
                [
                    'title' => 'Brands',
                    'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
                    'bullet' => 'line',
                    'root' => true,
                    'submenu' => [
                        [
                            'title' => 'Add New',
                            'page' => 'admin/brand/add'
                        ],
                        [
                            'title' => 'Lists',
                            'page' => 'admin/brand/'
                        ],
                    ],
                ],
                [
                    'title' => 'Category',
                    'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
                    'bullet' => 'line',
                    'root' => true,
                    'submenu' => [
                        [
                            'title' => 'Add New',
                            'page' => 'admin/category/add'
                        ],
                        [
                            'title' => 'Lists',
                            'page' => 'admin/category/'
                        ],
                    ],
                ],
                [
                    'title' => 'Attributes',
                    'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
                    'bullet' => 'line',
                    'root' => true,
                    'submenu' => [
                        [
                            'title' => 'Add New',
                            'page' => 'admin/attributes/add'
                        ],
                        [
                            'title' => 'Lists',
                            'page' => 'admin/attributes/'
                        ],
                    ],
                ],
                [
                    'title' => 'Products',
                    'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
                    'bullet' => 'line',
                    'root' => true,
                    'submenu' => [
                        [
                            'title' => 'Add New',
                            'page' => 'admin/products/add'
                        ],
                        [
                            'title' => 'Lists',
                            'page' => 'admin/products/'
                        ],
                    ],
                ],
                [
                    'title' => 'Feedbacks',
                    'icon' => 'fa fa-star',
                    'page' => 'admin/feedbacks/'
                ],
            ],
        ],
        [
            'title' => 'Coupon Code',
            'icon' => 'media/svg/icons/Code/Code.svg',
            'bullet' => 'line',
            'root' => true,
            'submenu' => [
                [
                    'title' => 'Add New',
                    'page' => 'admin/coupons/add'
                ],
                [
                    'title' => 'Lists',
                    'page' => 'admin/coupons/'
                ],
            ],
        ],
        [
            'title' => 'Orders',
            'icon' => 'fa fa-reorder',
            'bullet' => 'dot',
            'root' => true,
            'submenu' => [
                [
                    'title' => 'Orders Status',
                    'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
                    'bullet' => 'line',
                    'root' => true,
                    'submenu' => [
                        [
                            'title' => 'Add New',
                            'page' => 'admin/orderstatus/add'
                        ],
                        [
                            'title' => 'Lists',
                            'page' => 'admin/orderstatus/'
                        ],
                    ],
                ],
                [
                    'title' => 'Order List',
                    'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
                    'bullet' => 'line',
                    'page' => 'admin/orders/'
                ],
                [
                    'title' => 'Bulk Order',
                    'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
                    'bullet' => 'line',
                    'page' => 'admin/bulk-order/'
                ],
            ],
        ],
        [
            'title' => 'Blog',
            'icon' => 'fa fa-newspaper-o',
            'bullet' => 'line',
            'root' => true,
            'submenu' => [
                [
                    'title' => 'Add Blog',
                    'page' => 'admin/blogs/add'
                ],
                [
                    'title' => 'Blogs',
                    'page' => 'admin/blogs/'
                ],
                [
                    'title' => 'Add Blog Category',
                    'page' => 'admin/blogcategories/add'
                ],
                [
                    'title' => 'Blog Categories',
                    'page' => 'admin/blogcategories/'
                ],

            ],
        ],
        [
            'title' => 'Pages',
            'icon' => 'fa fa-columns',
            'bullet' => 'line',
            'root' => true,
            'submenu' => [
                [
                    'title' => 'Add New',
                    'page' => 'admin/pages/add'
                ],
                [
                    'title' => 'Pages',
                    'page' => 'admin/pages/'
                ]
            ],
        ],
      /*  [
            'title' => 'Import',
            'icon' => 'fa fa-file-excel-o',
            'page' => 'admin/import/'
        ],*/
        [
            'title' => 'Sliders',
            'icon' => 'fa fa-file-image-o',
            'bullet' => 'line',
            'root' => true,
            'submenu' => [
                [
                    'title' => 'Add New',
                    'page' => 'admin/sliders/add'
                ],
                [
                    'title' => 'Sliders',
                    'page' => 'admin/sliders/'
                ]
            ],
        ],
        [
            'title' => 'Home',
            'icon' => 'fa fa-home',
            'page' => 'admin/home-page/'
        ],
        [
            'title' => 'Settings',
            'icon' => 'fa fa-gear',
            'page' => 'admin/settings/'
        ],
        [
            'title' => 'Import',
            'icon' => 'fa fa-upload',
            'page' => 'admin/import/'
        ],
        [
            'title' => 'Access Request',
            'icon' => 'fa fa-question',
            'page' => 'admin/vendor-enquiry/'
        ],
    ]

];
