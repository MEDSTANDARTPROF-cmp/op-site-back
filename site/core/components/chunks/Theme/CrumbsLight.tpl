[[pdoCrumbs?
	&showAtHome=`0`
	&showHome=`1`
	&tplWrapper=`@INLINE <ol class="breadcrumb opacity-50">[[+output]]</ol>`
	&tpl=`@INLINE <li class="breadcrumb-item text-light"><a class="link-light text-decoration-none" href="[[+link]]">[[+menutitle]]</a></li>`
	&tplCurrent=`@INLINE <li class="breadcrumb-item active text-light" aria-current="page">[[+menutitle]]</li>`
]]
<style>
.breadcrumb-item+.breadcrumb-item::before {color: inherit;}
</style>