<style>
*{padding:0;margin:0}
html, body {
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
    background-color: white;
    -webkit-perspective: 1500;
    perspective: 1500;
    font-family: 'Roboto', sans-serif;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
body{background: linear-gradient(120deg, rgba(50, 150, 100, 0.4), rgba(0, 0, 100, 0));}
.background {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    background: linear-gradient(120deg, rgba(50, 150, 100, 0.4), rgba(0, 0, 100, 0));
}
.background:before, .background:after {
    position: absolute;
    content: " ";
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(0deg, rgba(0, 0, 200, 0.4), rgba(0, 0, 200, 0));
}
.background:after {
    background: linear-gradient(240deg, rgba(150, 50, 50, 0.4), rgba(0, 0, 200, 0));
}
.step-bar-wrapper{
  font-size:0;
  background:#fff;
  text-align:center;padding:50px 0 0;
  box-shadow:5px 5px 24px 0px rgba(0, 0, 0, 0.2);
  width:600px;
  margin:30px auto 0;
  position:relative;
  z-index:10;
  border-radius:10px
}
a{
  color:#5c399e;/* change primary color */
}
.step-wrapper{
  padding:0;
  margin:0;
  font-size:0;
  display:flex;
  align-items: center;
  justify-content: center;
  counter-reset: step;
  tr
}
.step-wrapper li{
  width:120px;
}
.step-wrapper li > a:before {
	content:'';
	width: 36px;
	height: 36px;
	display: block;
	font-size: 16px;
  font-weight:700;
  background-color: transparent;
	border-radius: 100%;
  z-index:1;
  position:absolute;
  text-align:center;
}
.step-wrapper li > a:after {
	content: counter(step);
	counter-increment: step;
	width: 36px;
	line-height: 36px;
	display: block;
	font-size: 16px;
  color:#bbb;
  font-weight:700;
  background-color: transparent;
	border-radius: 100%;
  z-index:1;
  position:absolute;
  text-align:center;
}
.step-wrapper li.completed > a:after {
  content:'\2713';
  color: currentColor;
}
.step-wrapper li:first-of-type a:before,
.step-wrapper li:first-of-type a:after{
  margin-left:-21px;
}
.step-wrapper li:last-of-type > a:before,
.step-wrapper li:last-of-type > a:after{
  margin-left:21px;
}
.step-wrapper li.completed > a:before{
  background: #fff;
  color:#c4c4c4;
  -webkit-box-shadow:0px 2px 4px 0px rgba(0,0,0,0.15);
  box-shadow: 0px 2px 4px 0px rgba(0,0,0,0.15);
}
.step-wrapper li.active > a:before{
  background-color: currentColor;
  -webkit-box-shadow:0px 0px 0px 0px rgba(0,0,0,0.15), inset 0px 0px 0px 0px rgba(0,0,0,0.15), 0px 0px 9px 0px currentColor;
  background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(247, 247, 247,0.5)), to(rgba(231, 231, 231,.01)));
      background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(247, 247, 247,0.5)), to(rgba(231, 231, 231,.01)));
    background-image: -webkit-linear-gradient(top, rgba(247, 247, 247,0.5), rgba(231, 231, 231,.01));
    background-image: -moz-linear-gradient(top, rgba(247, 247, 247,0.5), rgba(231, 231, 231,.01));
    background-image: -ms-linear-gradient(top, rgba(247, 247, 247,0.5), rgba(231, 231, 231,.01));
    background-image: -o-linear-gradient(top, rgba(247, 247, 247,0.5), rgba(231, 231, 231,.01));
}
.step-wrapper li.active > a:after{
  color:#fff;
}
.step-wrapper li span{
  display:block;
  width:100%;
  text-align:center;
  margin-bottom:15px;
}
.step-wrapper li span a{
  font-size: 14px;
  font-weight:700;
}
.step-wrapper li:not(.active):not(.completed) span a{
  color: #bbb;
}
.step-wrapper li > a{
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  overflow:hidden;
  height:48px
}
.step-wrapper li:first-of-type > a{
  padding-left: 40px;
}
.step-wrapper li:last-of-type > a{
  padding-right:40px;
}
.step-wrapper li > a svg{
  height: 48px;
  min-height: 48px;
  width: auto;
  position: absolute;
  display: inline-block;
  stroke-width: 0;
  transition:all 300ms ease-in-out;
}
.step-wrapper li > a svg{
  filter:url(#inset-shadow);
}
a.button{
  margin:50px 15px;
  display:inline-block;
  border-radius:4px;
  width:100px;
  height:50px;
  text-align:center;
  line-height:50px;
  background-color: currentColor;
  -webkit-box-shadow:0px 2px 4px 0px rgba(0,0,0,0.15), inset 0px 0px 0px 2px rgba(0,0,0,0.15), 0px 0px 21px 0px currentColor;
  box-shadow:0px 2px 4px 0px rgba(0,0,0,0.15), inset 0px 0px 0px 2px rgba(0,0,0,0.15), 0px 0px 21px 0px currentColor;
  background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(247, 247, 247,0.5)), to(rgba(231, 231, 231,.01)));
      background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(247, 247, 247,0.5)), to(rgba(231, 231, 231,.01)));
    background-image: -webkit-linear-gradient(top, rgba(247, 247, 247,0.5), rgba(231, 231, 231,.01));
    background-image: -moz-linear-gradient(top, rgba(247, 247, 247,0.5), rgba(231, 231, 231,.01));
    background-image: -ms-linear-gradient(top, rgba(247, 247, 247,0.5), rgba(231, 231, 231,.01));
    background-image: -o-linear-gradient(top, rgba(247, 247, 247,0.5), rgba(231, 231, 231,.01));
}
a.button span{color:#fff;font-size:16px;}
</style>
<div class="background">
  <div class="step-bar-wrapper">
    <svg aria-hidden="true" style="position: absolute; width: 0; height: 0; overflow: hidden;" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
      <defs>
        <filter id="inset-shadow" x="-50%" y="-50%" width="200%" height="200%">
          <feComponentTransfer in="SourceAlpha">
            <feFuncA type="table" tableValues="1 0"/>
          </feComponentTransfer>
          <feGaussianBlur stdDeviation="1.2"/>
          <feOffset dx="0" dy="0.5" result="offsetblur"/>
          <feFlood flood-color="rgba(0, 0, 0, 0.5)" result="color"/>
          <feComposite in2="offsetblur" operator="in"/>
          <feComposite in2="SourceAlpha" operator="in"/>
          <feMerge>
            <feMergeNode in="SourceGraphic"/>
            <feMergeNode/>
          </feMerge>
        </filter>
        <symbol id="icon-left" viewBox="0 0 61 32">
          <title>left</title>
          <path id="first" fill="#ececec" d="M59.992 13.423h-23.73c-2.481 0-4.708-1.527-5.726-3.817-2.608-5.726-8.652-9.543-15.459-8.843-7.316 0.763-13.233 6.871-13.678 14.251-0.573 8.907 6.489 16.223 15.268 16.223 6.235 0 11.579-3.69 13.932-9.034 1.018-2.29 3.372-3.69 5.853-3.69h23.539v-5.089z"></path>
        </symbol>
        <symbol id="icon-mid" viewBox="0 0 89 32">
          <title>mid</title>
          <path id="mid" fill="#ececec" d="M64.26 13.501c-2.531 0-4.803-1.558-5.842-3.895-2.272-4.868-6.945-8.373-12.592-8.957-0.065 0-0.065 0-0.13 0s-0.065 0-0.13 0c-0.325 0-0.584-0.065-0.909-0.065-0.195 0-0.389 0-0.519 0-0.195 0-0.389 0-0.519 0-0.325 0-0.584 0-0.909 0.065-0.065 0-0.065 0-0.13 0s-0.065 0-0.13 0c-5.582 0.584-10.32 4.089-12.527 9.022-1.039 2.337-3.31 3.895-5.842 3.895h-24.146v5.193h24.016c2.531 0 4.933 1.428 5.972 3.765 2.207 4.998 6.945 8.568 12.592 9.152 0 0 0.065 0 0.065 0 0.195 0 0.454 0.065 0.649 0.065 0.26 0 0.454 0 0.714 0 0.065 0 0.13 0 0.195 0 0 0 0 0 0.065 0 0 0 0 0 0.065 0s0.13 0 0.195 0c0.26 0 0.454 0 0.714 0 0.195 0 0.454 0 0.649-0.065 0 0 0.065 0 0.065 0 5.647-0.584 10.385-4.154 12.592-9.152 1.039-2.337 3.44-3.765 5.972-3.765h24.016v-5.193h-24.211z"></path>
        </symbol>
        <symbol id="icon-right" viewBox="0 0 61 32">
          <title>right</title>
          <path id="last" fill="#ececec" d="M1.4 13.423h23.666c2.481 0 4.708-1.527 5.726-3.817 2.608-5.726 8.652-9.543 15.459-8.843 7.38 0.763 13.233 6.871 13.678 14.251 0.573 8.907-6.489 16.223-15.268 16.223-6.235 0-11.579-3.69-13.932-9.034-1.018-2.29-3.372-3.69-5.853-3.69h-23.539v-5.089z"></path>
        </symbol>
      </defs>
    </svg>

    <ul class="step-wrapper">
      <li class="completed">

        <span><a>Step 1</a></span>
        <a>
          <svg class="icon icon-left"><use xlink:href="#icon-left"></use></svg>
        </a>
      </li>
      <li class="active">
        <span><a>Step 2</a></span>
        <a>
          <svg class="step step-mid"><use xlink:href="#icon-mid"></use></svg>
        </a>
      </li>
      <li class="">
        <span><a>Step 3</a></span>
        <a>
          <svg class="step step-right"><use xlink:href="#icon-right"></use></svg>
        </a>
      </li>
    </ul>
    <a href="javascript:void(0)" class="button previous"><span>Previous</span></a>
  </div>
</div>