<?php

/* oauth2/server/base.twig */
class __TwigTemplate_015547d95ce4df3449c4407b9e27a601 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<!--[if lt IE 7]>      <html class=\"no-js lt-ie9 lt-ie8 lt-ie7\"> <![endif]-->
<!--[if IE 7]>         <html class=\"no-js lt-ie9 lt-ie8\"> <![endif]-->
<!--[if IE 8]>         <html class=\"no-js lt-ie9\"> <![endif]-->
<!--[if gt IE 8]><!--> <html class=\"no-js\"> <!--<![endif]-->
    <head>
        <meta charset=\"utf-8\">
        <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\">
        <title></title>
        <meta name=\"description\" content=\"\">
        <meta name=\"viewport\" content=\"width=device-width\">

        <link rel=\"stylesheet\" href=\"";
        // line 13
        echo twig_escape_filter($this->env, (isset($context["basePath"]) ? $context["basePath"] : null), "html", null, true);
        echo "/css/lockdin.css\">
        <link rel=\"stylesheet\" href=\"";
        // line 14
        echo twig_escape_filter($this->env, (isset($context["basePath"]) ? $context["basePath"] : null), "html", null, true);
        echo "/css/shared.css\">
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class=\"chromeframe\">You are using an outdated browser. <a href=\"http://browsehappy.com/\">Upgrade your browser today</a> or <a href=\"http://www.google.com/chromeframe/?redirect=true\">install Google Chrome Frame</a> to better experience this site.</p>
        <![endif]-->

        ";
        // line 21
        $this->env->loadTemplate("oauth2/analytics.twig")->display($context);
        // line 22
        echo "        ";
        $this->env->loadTemplate("oauth2/github.twig")->display($context);
        // line 23
        echo "        ";
        $this->env->loadTemplate("oauth2/server/header.html")->display($context);
        // line 24
        echo "
        <div id=\"container\">
            <article class=\"home\" role=\"main\">
                <div id=\"content\" role=\"main\">
                  ";
        // line 28
        $this->displayBlock('content', $context, $blocks);
        // line 30
        echo "                </div>
            </article>
        </div>
    </body>
</html>
";
    }

    // line 28
    public function block_content($context, array $blocks = array())
    {
        // line 29
        echo "                  ";
    }

    public function getTemplateName()
    {
        return "oauth2/server/base.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  76 => 29,  73 => 28,  64 => 30,  62 => 28,  56 => 24,  53 => 23,  48 => 21,  38 => 14,  34 => 13,  20 => 1,  60 => 28,  50 => 22,  31 => 4,  28 => 3,);
    }
}
