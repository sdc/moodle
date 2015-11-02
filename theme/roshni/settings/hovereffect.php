<?php

global $CFG,$DB;
$pluginname = 'theme_roshni';
$fieldname = 'effect';

$hovereffect = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$fieldname.'"');

if (!empty($hovereffect->value)) {
	if ($hovereffect->value == '"effect1"') { //first effect 
?>
<style>
	.categories-item:hover:after {
		opacity: 1 !important;
	}
	.categories-item:hover .categories-item-cont {
		opacity: 1 !important;
	}
</style>

<?php } else if ($hovereffect->value == '"effect2"') { //second effect ?>
<style>
	.view {
	   overflow: hidden;
	   position: relative;
	   text-align: center;
	   
	   cursor: default;
	}
	.view .mask {
	   width: 100%;
	   position: absolute;
	   overflow: hidden;
	   top: 0;
	   left: 0;
	}
	.fourth-effect .mask {
		position:absolute; /* Center the mask */
		top:50px;
		left:100px;
		cursor:pointer;
	    border-radius: 50px;
	    border-width: 50px;
	    display: inline-block;
	    height: 100px;
	    width: 100px;
		border: 50px solid rgba(0, 0, 0, 0.7);
		-moz-box-sizing:border-box;
	    -webkit-box-sizing:border-box;
	    box-sizing:border-box;
		opacity:1;
		visibility:visible;
		-moz-transform:scale(4);
		-webkit-transform:scale(4);
		-o-transform:scale(4);
		-ms-transform:scale(4);
		transform:scale(4);
		-moz-transition:all 0.3s ease-in-out;
		-webkit-transition:all 0.3s ease-in-out;
		-o-transition:all 0.3s ease-in-out;
		-ms-transition:all 0.3s ease-in-out;
		transition:all 0.3s ease-in-out;
	}
	.fourth-effect:hover .mask {
	   opacity: 0;
	   border:0px solid rgba(0,0,0,0.7);
	   visibility:hidden;
	}
	@media screen and (max-width: 1920px) {
		.fourth-effect .mask {
			position:absolute; /* Center the mask */
			top:50px;
			left:180px;
			cursor:pointer;
		    border-radius: 50px;
		    border-width: 50px;
		    display: inline-block;
		    height: 100px;
		    width: 100px;
			border: 50px solid rgba(0, 0, 0, 0.7);
			-moz-box-sizing:border-box;
		    -webkit-box-sizing:border-box;
		    box-sizing:border-box;
			opacity:1;
			visibility:visible;
			-moz-transform:scale(8, 8);
			-webkit-transform:scale(8, 8);
			-o-transform:scale(8, 8);
			-ms-transform:scale(8, 8);
			transform:scale(8, 8);
			-moz-transition:all 0.3s ease-in-out;
			-webkit-transition:all 0.3s ease-in-out;
			-o-transition:all 0.3s ease-in-out;
			-ms-transition:all 0.3s ease-in-out;
			transition:all 0.3s ease-in-out;
		}
	}
	@media screen and (max-width: 1280px) {
		.fourth-effect .mask {
			position:absolute; /* Center the mask */
			top:50px;
			left:130px;
			cursor:pointer;
		    border-radius: 50px;
		    border-width: 50px;
		    display: inline-block;
		    height: 100px;
		    width: 100px;
			border: 50px solid rgba(0, 0, 0, 0.7);
			-moz-box-sizing:border-box;
		    -webkit-box-sizing:border-box;
		    box-sizing:border-box;
			opacity:1;
			visibility:visible;
			-moz-transform:scale(8, 8);
			-webkit-transform:scale(8, 8);
			-o-transform:scale(8, 8);
			-ms-transform:scale(8, 8);
			transform:scale(8, 8);
			-moz-transition:all 0.3s ease-in-out;
			-webkit-transition:all 0.3s ease-in-out;
			-o-transition:all 0.3s ease-in-out;
			-ms-transition:all 0.3s ease-in-out;
			transition:all 0.3s ease-in-out;
		}
	}
</style>
	<?php } else if ($hovereffect->value == '"effect3"') { //third effect ?>
		<style>
		.view {
		   overflow: hidden;
		   position: relative;
		   text-align: center;
		   box-shadow: 0px 0px 5px #aaa;
		   cursor: default;
		}
		.view .mask{
		   width: 100%;
		   /*height: 200px;*/
		   position: absolute;
		   overflow: hidden;
		   top: 0;
		   left: 0;
		}
		.fourth-effect .mask {
		   opacity: 0;
		   overflow:visible;
		   border:0px solid rgba(0,0,0,0.7);
		   -moz-box-sizing:border-box;
		   -webkit-box-sizing:border-box;
		   box-sizing:border-box;
		   -webkit-transition: all 0.4s ease-in-out;
		   -moz-transition: all 0.4s ease-in-out;
		   -o-transition: all 0.4s ease-in-out;
		   -ms-transition: all 0.4s ease-in-out;
		   transition: all 0.4s ease-in-out;


		}
		.fourth-effect:hover .mask {
   			opacity: 1;
   			border:100px solid rgba(0,0,0,0.7);
		}
		</style>
	<?php } else if ($hovereffect->value == '"effect4"') { //fourth effect ?>
	<style>
		.fourth-effect .mask {
		   opacity: 0;
		   overflow:visible;
		   border:0px solid rgba(0,0,0,0.7);
		   -moz-box-sizing:border-box;
		   -webkit-box-sizing:border-box;
		   box-sizing:border-box;
		   -webkit-transition: all 0.4s ease-in-out;
		   -moz-transition: all 0.4s ease-in-out;
		   -o-transition: all 0.4s ease-in-out;
		   -ms-transition: all 0.4s ease-in-out;
		   transition: all 0.4s ease-in-out;
		}

		.fourth-effect:hover .mask {
		   opacity: 1;
		   border:50px solid rgba(0,0,0,0.7);

		}
		.view .mask{
		   width: 100%;
		   /*height: 200px;*/
		   position: absolute;
		   /*overflow: hidden;*/
		   top: 0;
		   left: 0;
		}
	</style>
<?php } else if ($hovereffect->value == '"effect5"') { //fifth effect ?>
	<style>
		.fourth-effect .mask {
		   cursor:pointer;
		   opacity:1;
		   visibility:visible;
		   border:100px solid rgba(0,0,0,0.7);
		   -moz-box-sizing:border-box;
		   -webkit-box-sizing:border-box;
		   box-sizing:border-box;
		   -moz-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -webkit-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -o-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -ms-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		}
		.fourth-effect:hover .mask {
			border:0px double rgba(0,0,0,0.7);
			opacity:0;
			visibility:hidden;
		}
		.view .mask{
		   width: 100%;
		   /*height: 200px;*/
		   position: absolute;
		   /*overflow: hidden;*/
		   top: 0;
		   left: 0;
		}
		@media screen and (max-width: 1920px) {
			.fourth-effect .mask {
		   cursor:pointer;
		   opacity:1;
		   visibility:visible;
		   border:150px solid rgba(0,0,0,0.7);
		   -moz-box-sizing:border-box;
		   -webkit-box-sizing:border-box;
		   box-sizing:border-box;
		   -moz-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -webkit-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -o-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -ms-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		}
		@media screen and (max-width: 1280px) {
			.fourth-effect .mask {
		   cursor:pointer;
		   opacity:1;
		   visibility:visible;
		   border:101px solid rgba(0,0,0,0.7);
		   -moz-box-sizing:border-box;
		   -webkit-box-sizing:border-box;
		   box-sizing:border-box;
		   -moz-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -webkit-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -o-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -ms-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		}
		@media screen and (max-width: 1024px) {
			.fourth-effect .mask {
		   cursor:pointer;
		   opacity:1;
		   visibility:visible;
		   border:80px solid rgba(0,0,0,0.7);
		   -moz-box-sizing:border-box;
		   -webkit-box-sizing:border-box;
		   box-sizing:border-box;
		   -moz-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -webkit-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -o-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -ms-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		}
		@media screen and (max-width: 990px) {
			.fourth-effect .mask {
		   cursor:pointer;
		   opacity:1;
		   visibility:visible;
		   border:77px solid rgba(0,0,0,0.7);
		   -moz-box-sizing:border-box;
		   -webkit-box-sizing:border-box;
		   box-sizing:border-box;
		   -moz-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -webkit-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -o-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -ms-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		}
		@media screen and (max-width: 900px) {
			.fourth-effect .mask {
		   cursor:pointer;
		   opacity:1;
		   visibility:visible;
		   border:141px solid rgba(0,0,0,0.7);
		   -moz-box-sizing:border-box;
		   -webkit-box-sizing:border-box;
		   box-sizing:border-box;
		   -moz-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -webkit-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -o-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -ms-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		}
		@media screen and (max-width: 800px) {
			.fourth-effect .mask {
		   cursor:pointer;
		   opacity:1;
		   visibility:visible;
		   border:125px solid rgba(0,0,0,0.7);
		   -moz-box-sizing:border-box;
		   -webkit-box-sizing:border-box;
		   box-sizing:border-box;
		   -moz-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -webkit-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -o-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -ms-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		}
		@media screen and (max-width: 768px) {
			.fourth-effect .mask {
		   cursor:pointer;
		   opacity:1;
		   visibility:visible;
		   border:121px solid rgba(0,0,0,0.7);
		   -moz-box-sizing:border-box;
		   -webkit-box-sizing:border-box;
		   box-sizing:border-box;
		   -moz-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -webkit-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -o-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -ms-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		}
		@media screen and (max-width: 640px) {
			.fourth-effect .mask {
		   cursor:pointer;
		   opacity:1;
		   visibility:visible;
		   border:101px solid rgba(0,0,0,0.7);
		   -moz-box-sizing:border-box;
		   -webkit-box-sizing:border-box;
		   box-sizing:border-box;
		   -moz-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -webkit-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -o-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -ms-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		}
		@media screen and (max-width: 600px) {
			.fourth-effect .mask {
		   cursor:pointer;
		   opacity:1;
		   visibility:visible;
		   border:94px solid rgba(0,0,0,0.7);
		   -moz-box-sizing:border-box;
		   -webkit-box-sizing:border-box;
		   box-sizing:border-box;
		   -moz-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -webkit-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -o-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -ms-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		}
		@media screen and (max-width: 480px) {
			.fourth-effect .mask {
		   cursor:pointer;
		   opacity:1;
		   visibility:visible;
		   border:76px solid rgba(0,0,0,0.7);
		   -moz-box-sizing:border-box;
		   -webkit-box-sizing:border-box;
		   box-sizing:border-box;
		   -moz-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -webkit-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -o-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -ms-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		}
		@media screen and (max-width: 360px) {
			.fourth-effect .mask {
		   cursor:pointer;
		   opacity:1;
		   visibility:visible;
		   border:113px solid rgba(0,0,0,0.7);
		   -moz-box-sizing:border-box;
		   -webkit-box-sizing:border-box;
		   box-sizing:border-box;
		   -moz-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -webkit-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -o-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -ms-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		}
		@media screen and (max-width: 320px) {
			.fourth-effect .mask {
		   cursor:pointer;
		   opacity:1;
		   visibility:visible;
		   border:100px solid rgba(0,0,0,0.7);
		   -moz-box-sizing:border-box;
		   -webkit-box-sizing:border-box;
		   box-sizing:border-box;
		   -moz-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -webkit-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -o-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   -ms-transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		   transition: all 0.4s cubic-bezier(0.940, 0.850, 0.100, 0.620);
		}
		
		}
	</style>
<?php }
}//end of main if ?>

