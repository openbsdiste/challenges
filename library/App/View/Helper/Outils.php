<?php
    class App_View_Helper_Outils extends Zend_View_Helper_Abstract {
        function outils ($boutons) {
            $html = <<<EOT
<li>
    <h2><span>Outils</span></h2>
    <div style="overflow: auto">
        <h3>Outils</h3><br />
EOT;
            foreach ($boutons as $bouton) {
                $id = $bouton ['id'];
                $value = $bouton ['value'];
                $desc = $bouton ['desc'];
                $html .= <<<EOT
        <p>
            <input id="$id" type="button" class="ui-state-default ui-corner-all" value="$value"></input>
            <span>$desc</span>
        </p><br />
EOT;
            }
            $html .= "    </div>\n</li>\n";
            return $html;
        }
    }