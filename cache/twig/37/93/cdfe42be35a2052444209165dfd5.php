<?php

/* oauth2/server/authorize.twig */
class __TwigTemplate_3793cdfe42be35a2052444209165dfd5 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("oauth2/server/base.twig");

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "oauth2/server/base.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = array())
    {
        // line 4
        echo "    <p>
        <strong>Demo App</strong> would like to access the following data:
    </p>
    <ul>
        <li>friends</li>
        <li>memories</li>
        <li>hopes, dreams, passions, etc.</li>
        <li>sock drawer</li>
    </ul>
    <p>It will use this data to:</p>
    <ul>
        <li>integrate with friends</li>
        <li>make your life better</li>
        <li>miscellaneous nefarious purposes</li>
    </ul>
    <ul class=\"authorize_options\">
        <li>
            <form action=\"";
        // line 21
        echo twig_escape_filter($this->env, (("authorize" . "?") . $this->getAttribute((isset($context["response"]) ? $context["response"] : null), "queryString")), "html", null, true);
        echo "\" method=\"post\">
                <input type=\"submit\" class=\"button authorize\"
                       value=\"Yes, I Authorize This Request\"/>
                <input type=\"hidden\" name=\"authorize\" value=\"1\"/>
            </form>
        </li>
        <li class=\"cancel\">
            <form id=\"cancel\" action=\"";
        // line 28
        echo twig_escape_filter($this->env, (("authorize" . "?") . $this->getAttribute((isset($context["response"]) ? $context["response"] : null), "queryString")), "html", null, true);
        echo "\"
                  method=\"post\">
                <a href=\"#\"
                   onclick=\"document.getElementById('cancel').submit()\">cancel</a>
                <input type=\"hidden\" name=\"authorize\" value=\"0\"/>
            </form>
        </li>
    </ul>
";
    }

    public function getTemplateName()
    {
        return "oauth2/server/authorize.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  60 => 28,  50 => 21,  31 => 4,  28 => 3,);
    }
}
