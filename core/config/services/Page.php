<?php
/**
 * =======================================================
 * @Description :TODO
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */
return [
    'page' => [
        'class' => 'core\services\Page',
        //'terry' => 'xxxx',
        // 子服务
        'childService' => [
            'asset' => [
                'class' =>  'core\services\page\Asset',
                // js 版本号，当更改了js，将这里的版本号+1，生成的js链接就会更改为  xxx.js?v=2 ,
                // 这样做的好处是，js的链接url改变了，可以防止浏览器继续使用缓存，而不是重新加载js文件的问题。
                'jsVersion'        => 1,
                // css 版本号，原理同js
                // 关于版本号更多的信息，请参看：http://www.fancyecommerce.com/2017/04/17/css-js-%E5%90%8E%E9%9D%A2%E5%8A%A0%E7%89%88%E6%9C%AC%E5%8F%B7%E7%9A%84%E5%8E%9F%E5%9B%A0%E5%92%8C%E6%96%B9%E5%BC%8F/
                'cssVersion'    => 1,
                /**
                 * @var string the root directory string the published asset files.
                 * 设置: js和css的发布路径，默认在web路径下的assets文件夹下，您可以放到其他的文件路径，然后用独立的jscss域名做指向
                 * 譬如设置为：'@appimage/assets'，也可以将 @appimage 换成绝对路径
                 */
                'basePath' => '@webroot/assets',
                /**
                 * @var string the base URL through which the published asset files can be accessed.
                 * 设置: js和css的URL路径
                 * 可以将 @web 换成域名 ， 譬如  `http:://www/fecshop.com/assets`
                 * 这样就可以将js和css文件使用独立的域名了【把域名对应的地址对应到$basePath】。
                 */
                'baseUrl' => '@web/assets',
                // 是否每次刷新，强制发布js css到线上？ 开发环境设置为true，正式环境设置为false（你也可以设置为true，但是每次刷新都会复制js和css文件到@app/web/assets/下面，耗费资源）
                // 线上设置成false，每次访问不会强制复制js和css到发布环境，可以节省资源，但是，当css和js更新后，
                // 需要去@app/web/assets/ 路径下，手动清空所有的文件夹和文件，当assets路径下找不到文件，就会重新复制库包里的js和css到web环境，
                // 这是属于Yii2的知识范畴。
                'forceCopy' => true,
                
                
                /* js and css config example:
                'jsOptions'	=> [
                    # js config 1
                    [
                        'options' => [
                            'position' =>  'POS_END',
                        //	'condition'=> 'lt IE 9',
                        ],
                        'js'	=>[
                            'js/jquery-3.0.0.min.js',
                            'js/js.js',
                        ],
                    ],
                    # js config 2
                    [
                        'options' => [
                            'condition'=> 'lt IE 9',
                        ],
                        'js'	=>[
                            'js/ie9js.js'
                        ],
                    ],
                ],
                # css config
                'cssOptions'	=> [
                    # css config 1.
                    [
                        'css'	=>[
                            'css/style.css',
                            'css/ie.css',
                        ],
                    ],

                    # css config 2.
                    [
                        'options' => [
                            'condition'=> 'lt IE 9',
                        ],
                        'css'	=>[
                            'css/ltie9.css',
                        ],
                    ],
                ],
                */
            ],

            'theme' => [
                'class'        => 'core\services\page\Theme',
                /*
                 *
                # 这里是设置本地二开模板路径，如果您在每一个store中
                # 进行了配置，这里将被覆盖。
                'localThemeDir' 	=> '@appfront/theme/terry/theme01',
                # 这里设置的是第三方的模板路径
                'thirdThemeDir'		=> [],
                # 在文件 @fecshop/app/appName/modules/AppfrontController.php 初始化
                # 这里是fecshop的模板路径。
                #'fecshopThemeDir'	=> '',
                */
            ],
            'widget' => [
                'class'        => 'core\services\page\Widget',
                'widgetConfig' => [
                    'head' => [
                        # 动态数据提供部分
                        'class' => 'fecshop\app\appfront\widgets\Head',
                        # 根据多模板的优先级，依次去模板找查找该文件，直到找到这个文件。
                        'view'  => 'widgets/head.php',
                        # 缓存
                        'cache' => [
                            'enable'	=> false, # 是否开启
                            'timeout' 	=> 4500,  # 缓存过期时间
                        ],
                    ],
                    'header' => [
                        'class' => 'fecshop\app\appfront\widgets\Headers',
                        # 根据多模板的优先级，依次去模板找查找该文件，直到找到这个文件。
                        'view'  => 'widgets/header.php',
                        'cache' => [
                            'enable'	=> false,
                            'timeout' 	=> 4500,
                        ],
                    ],
                    'nav' => [
                        'view'  => 'widgets/nav.php',
                    ],
                    'menu' => [
                        'class' => 'appadmin\widgets\Menu',
                        # 根据多模板的优先级，依次去模板找查找该文件，直到找到这个文件。
                        'view'  => 'widgets/menu.php',
                        'cache' => [
                            'enable'	=> true,
                            'timeout' 	=> 0,
                        ],
                    ],
                    
                    'flashmessage' => [
                        'view'  => 'widgets/flashmessage.php',
                    ],
                    'datatables' => [
                        'class' => 'appadmin\widgets\DataTables',
                        'view'  => 'widgets/datatables.php',
                        'cache' => [
                            'enable'	=> false,
                            //'timeout' 	=> 4500,
                        ],
                    ],
                    'metable' => [
                        'class' => 'appadmin\widgets\MeTable',
                        'view'  => 'widgets/metable.php',
                        'cache' => [
                            'enable'	=> false,
                            //'timeout' 	=> 4500,
                        ],
                    ],
                    'nestable' => [
                        'class' => 'appadmin\widgets\Nestable',
                        'view'  => 'widgets/nestable.php',
                        'cache' => [
                            'enable'	=> false,
                            //'timeout' 	=> 4500,
                        ],
                    ],
                    'breadcrumbs' => [
                        'view'  => 'widgets/breadcrumbs.php',
                    ],
                    'footer' => [
                        'view'  => 'widgets/footer.php',
                    ],
                ]
            ],

            'staticblock' => [
                'class'        => 'core\services\page\StaticBlock',
            ],

            'sidebar' => [
                'class' => 'core\services\page\Sidebar',
                'options' => [
                    'id' => 'nav-list-main',
                    'class' => 'nav nav-list',
                ],
                'labelName' => 'menu_name',
                //'items' =>  $this->params['menus'],
                'itemsName' => 'child'

            ],
            'message' => [
                'class' => 'core\services\page\Message',

            ],
            'ueditor' => [
                'class' => 'core\services\page\Ueditor',
                'config'=> file_exists((__DIR__ . '/ueditor.json')) ? json_decode(preg_replace("/\/\*[\s\S]+?\*\//", '', file_get_contents(__DIR__ . '/ueditor.json')), true) :'',
            ],
        ],
    ],
];
