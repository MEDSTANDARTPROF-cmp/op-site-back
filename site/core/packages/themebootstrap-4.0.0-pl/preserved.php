<?php return array (
  'c347260b446adaffbf4f34795f463bd4' => 
  array (
    'criteria' => 
    array (
      'name' => 'themebootstrap',
    ),
    'object' => 
    array (
      'name' => 'themebootstrap',
      'path' => '{core_path}components/themebootstrap/',
      'assets_path' => '',
    ),
  ),
  'a86dd2c4b7ff554354a828813894625b' => 
  array (
    'criteria' => 
    array (
      'key' => 'themebootstrap_use_jquery',
    ),
    'object' => 
    array (
      'key' => 'themebootstrap_use_jquery',
      'value' => '1',
      'xtype' => 'combo-boolean',
      'namespace' => 'themebootstrap',
      'area' => '',
      'editedon' => '2025-02-07 15:25:55',
    ),
  ),
  '0ddcfc2cd2fdbfa503cd3e1bc0f71930' => 
  array (
    'criteria' => 
    array (
      'category' => 'Theme.Bootstrap',
    ),
    'object' => 
    array (
      'id' => 1,
      'parent' => 0,
      'category' => 'Theme.Bootstrap',
      'rank' => 0,
    ),
  ),
  '568a0135db9fea06c05c4bdbb5fef60d' => 
  array (
    'criteria' => 
    array (
      'name' => 'Head',
    ),
    'object' => 
    array (
      'id' => 1,
      'source' => 1,
      'property_preprocess' => 0,
      'name' => 'Head',
      'description' => 'Scripts and styles',
      'editor_type' => 0,
      'category' => 1,
      'cache_type' => 0,
      'snippet' => '<meta charset="[[++modx_charset]]">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="[[*description]]">
<base href="[[++site_url]]" />
<title>[[!pdoTitle]] / [[++site_name]]</title>
<link href="[[++assets_url]]components/themebootstrap/css/bootstrap.min.css" rel="stylesheet">',
      'locked' => 0,
      'properties' => NULL,
      'static' => 0,
      'static_file' => 'core/components/themebootstrap/elements/chunks/head.tpl',
      'content' => '<meta charset="[[++modx_charset]]">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="[[*description]]">
<base href="[[++site_url]]" />
<title>[[!pdoTitle]] / [[++site_name]]</title>
<link href="[[++assets_url]]components/themebootstrap/css/bootstrap.min.css" rel="stylesheet">',
    ),
  ),
  '7685107c9d8dd1981926abe3ec8bf06e' => 
  array (
    'criteria' => 
    array (
      'name' => 'Navbar',
    ),
    'object' => 
    array (
      'id' => 2,
      'source' => 1,
      'property_preprocess' => 0,
      'name' => 'Navbar',
      'description' => 'Navbar chunk',
      'editor_type' => 0,
      'category' => 1,
      'cache_type' => 0,
      'snippet' => '<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container">
        <a class="navbar-brand" href="/">[[++site_name]]</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbar">
            <ul class="navbar-nav me-auto d-flex ">
                [[pdoMenu?
                    &startId=`0`
                    &level=`3`
                    &tplOuter=`@INLINE [[+wrapper]]`
                    &tpl=`@INLINE
                    <li class="nav-item [[+classnames]]">
                        <a class="nav-link" href="[[+link]]" [[+attributes]]>[[+menutitle]]</a>
                    </li>`
                    &tplParentRow=`@INLINE
                    <li class="nav-item dropdown [[+classnames]]">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">[[+menutitle]]</a>
                        <ul class="dropdown-menu">[[+wrapper]]</ul>
                    </li>`
                ]]
            </ul>
        </div>
    </div>
</nav>',
      'locked' => 0,
      'properties' => 'a:0:{}',
      'static' => 0,
      'static_file' => 'core/components/themebootstrap/elements/chunks/navbar.tpl',
      'content' => '<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container">
        <a class="navbar-brand" href="/">[[++site_name]]</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbar">
            <ul class="navbar-nav me-auto d-flex ">
                [[pdoMenu?
                    &startId=`0`
                    &level=`3`
                    &tplOuter=`@INLINE [[+wrapper]]`
                    &tpl=`@INLINE
                    <li class="nav-item [[+classnames]]">
                        <a class="nav-link" href="[[+link]]" [[+attributes]]>[[+menutitle]]</a>
                    </li>`
                    &tplParentRow=`@INLINE
                    <li class="nav-item dropdown [[+classnames]]">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">[[+menutitle]]</a>
                        <ul class="dropdown-menu">[[+wrapper]]</ul>
                    </li>`
                ]]
            </ul>
        </div>
    </div>
</nav>',
    ),
  ),
  '60fd07c87642785d33be4f2c9779aae4' => 
  array (
    'criteria' => 
    array (
      'name' => 'Footer',
    ),
    'object' => 
    array (
      'id' => 3,
      'source' => 1,
      'property_preprocess' => 0,
      'name' => 'Footer',
      'description' => 'Footer chunk',
      'editor_type' => 0,
      'category' => 1,
      'cache_type' => 0,
      'snippet' => '<footer class="mt-5">
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-6 text-center text-md-start">
                <ul class="list-unstyled">
                    <li><small>total time: [^t^]</small></li>
                    <li><small>query time: [^qt^]</small></li>
                    <li><small>queries: [^q^]</small></li>
                    <li><small>memory: [^m^]</small></li>
                </ul>
            </div>
            <div class="col-12 col-md-6 text-center text-md-end">
                &copy;&nbsp;[[++site_name]]
            </div>
        </div>
    </div>
</footer>
<script src="[[++assets_url]]components/themebootstrap/js/bootstrap.bundle.min.js"></script>
[[++themebootstrap_use_jquery:is=`1`:then=`<script src="[[++assets_url]]components/themebootstrap/js/jquery-3.7.1.min.js"></script>`]]',
      'locked' => 0,
      'properties' => NULL,
      'static' => 0,
      'static_file' => 'core/components/themebootstrap/elements/chunks/footer.tpl',
      'content' => '<footer class="mt-5">
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-6 text-center text-md-start">
                <ul class="list-unstyled">
                    <li><small>total time: [^t^]</small></li>
                    <li><small>query time: [^qt^]</small></li>
                    <li><small>queries: [^q^]</small></li>
                    <li><small>memory: [^m^]</small></li>
                </ul>
            </div>
            <div class="col-12 col-md-6 text-center text-md-end">
                &copy;&nbsp;[[++site_name]]
            </div>
        </div>
    </div>
</footer>
<script src="[[++assets_url]]components/themebootstrap/js/bootstrap.bundle.min.js"></script>
[[++themebootstrap_use_jquery:is=`1`:then=`<script src="[[++assets_url]]components/themebootstrap/js/jquery-3.7.1.min.js"></script>`]]',
    ),
  ),
  'a39210bef4413e3f8795c16a43036829' => 
  array (
    'criteria' => 
    array (
      'name' => 'Content',
    ),
    'object' => 
    array (
      'id' => 4,
      'source' => 1,
      'property_preprocess' => 0,
      'name' => 'Content',
      'description' => 'Content chunk',
      'editor_type' => 0,
      'category' => 1,
      'cache_type' => 0,
      'snippet' => '<h3>[[*pagetitle]]</h3>
<article>
	[[*content]]
</article>',
      'locked' => 0,
      'properties' => NULL,
      'static' => 0,
      'static_file' => 'core/components/themebootstrap/elements/chunks/content.tpl',
      'content' => '<h3>[[*pagetitle]]</h3>
<article>
	[[*content]]
</article>',
    ),
  ),
  '5a47f05da5d537cba25fe9dc2864e4bc' => 
  array (
    'criteria' => 
    array (
      'name' => 'Crumbs',
    ),
    'object' => 
    array (
      'id' => 5,
      'source' => 1,
      'property_preprocess' => 0,
      'name' => 'Crumbs',
      'description' => 'Breadcrumb chunk',
      'editor_type' => 0,
      'category' => 1,
      'cache_type' => 0,
      'snippet' => '[[pdoCrumbs?
	&showAtHome=`0`
	&showHome=`1`
]]',
      'locked' => 0,
      'properties' => NULL,
      'static' => 0,
      'static_file' => 'core/components/themebootstrap/elements/chunks/crumbs.tpl',
      'content' => '[[pdoCrumbs?
	&showAtHome=`0`
	&showHome=`1`
]]',
    ),
  ),
  'd6b3228b5cc2b464f65049b74fdef5e0' => 
  array (
    'criteria' => 
    array (
      'templatename' => 'Bootstrap',
    ),
    'object' => 
    array (
      'id' => 2,
      'source' => 1,
      'property_preprocess' => 0,
      'templatename' => 'Bootstrap',
      'description' => 'Bootstrap template',
      'editor_type' => 0,
      'category' => 1,
      'icon' => '',
      'template_type' => 0,
      'content' => '<!DOCTYPE html>
<html lang="en">
	<head>
		[[$Head]]
	</head>
	<body>
		[[$Navbar]]
		<div class="container mt-3">
			[[$Crumbs]]
			[[$Content]]
		</div>
		[[$Footer]]
	</body>
</html>
',
      'locked' => 0,
      'properties' => NULL,
      'static' => 0,
      'static_file' => 'core/components/themebootstrap/elements/templates/bootstrap.tpl',
    ),
  ),
);