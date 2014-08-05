<?php

/* oauth2/server/header.html */
class __TwigTemplate_22e32b75c7a22295f0ad016a641cb25e extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<div id=\"header\" class=\"global-nav member\">
 <div class=\"top-nav\">
 <div class=\"wrapper\">

 <ul class=\"util\" role=\"navigation\">
   <li class=\"tab username-cont\">
     <a href=\"#\" class=\"tab-name username\">You <span class=\"menu-indicator\"></span></a>
   </li>
 </ul>

 <h2 class=\"logo\"><a href=\"#\" title=\"Home\">Lock'd <span>in</span></a></h2>

 <div class=\"account\">
 <span>You'll never get your data back</span>
 </div>
 </div>
 </div>

 <div class=\"bottom-nav\">
 <div class=\"wrapper\">

 </ul>
 </li>
  <ul class=\"nav\" role=\"navigation\">
    <li id=\"nav-primary-home\" class=\"tab selected\"><a href=\"#\" class=\"tab-name\"><span>Home</span></a></li>
    <li id=\"nav-primary-profile\" class=\"tab\"><a href=\"#\" class=\"tab-name\"><span>Profile</span></a></li>
    <li id=\"nav-primary-contacts\" class=\"tab \"><a href=\"/#\" class=\"tab-name\"><span>Contacts</span></a></li>
    <li id=\"nav-primary-groups\" class=\"tab \"><a href=\"#\" class=\"tab-name\"><span>Groups</span></a></li>
    <li id=\"nav-primary-jobs\" class=\"tab \"><a href=\"#\" class=\"tab-name\"><span>Jobs</span></a></li>
    <li id=\"nav-primary-inbox-links\" class=\"tab \"><a href=\"#\" class=\"tab-name\"><span>Inbox</span></a></li>
    <li id=\"nav-primary-company\" class=\"tab\"><a href=\"#\" class=\"tab-name\"><span>Companies</span></a></li>
    <li id=\"nav-primary-news\" class=\"tab\"><a href=\"#\" class=\"tab-name\"><span>News</span></a></li>
    <li id=\"nav-primary-more\" class=\"tab \"><a href=\"#\" class=\"tab-name\"><span>More</span></a></li>
 </ul>

 <form id=\"global-search\" role=\"search\" action=\"/search/fpsearch\" method=\"get\" accept-charset=\"UTF-8\" name=\"commonSearch\" class=\"global-search  \">
  <fieldset>
    <div class=\"search-scope styled-dropdown\" id=\"yui-gen1\"><span class=\"label\"> <span> People </span> </span></div>
    <span class=\"search-box-container\">
      <span id=\"search-autocomplete-container\" title=\"Tip: You can also search by keyword, company, school...\" class=\"/typeahead\">
        <label for=\"main-search-box\" class=\"ghost\" id=\"yui-gen2\" style=\"display: none; \">Search...</label>
        <input name=\"keywords\" id=\"main-search-box\" class=\"search-term yui-ac-input\" type=\"text\" value=\"\" autocomplete=\"off\" placeholder=\"Search...\">
        <span id=\"search-typeahead-container\"></span>
      </span>
      <input name=\"search\" value=\"Search\" class=\"search-go\" type=\"submit\">
    </span>
  </fieldset>
  <a id=\"search-link\" class=\"search-link\" href=\"/search?trk=advsrch\">Advanced</a>
 </form>
 </div>
 </div>
 </div>
";
    }

    public function getTemplateName()
    {
        return "oauth2/server/header.html";
    }

    public function getDebugInfo()
    {
        return array (  26 => 5,  21 => 2,  19 => 1,  76 => 29,  73 => 28,  64 => 30,  62 => 28,  56 => 24,  53 => 23,  48 => 21,  38 => 14,  34 => 13,  20 => 1,  60 => 28,  50 => 22,  31 => 4,  28 => 3,);
    }
}
