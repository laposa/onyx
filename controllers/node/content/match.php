<?php
/**
 * Copyright (c) 2018 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/node/content/default.php');
require_once('models/common/common_taxonomy.php');

class Onxshop_Controller_Node_Content_Match extends Onxshop_Controller_Node_Content_Default {

    /**
     * process content
     */
     
    public function processContent() {

        $node_id = $this->GET['id'];

        $Node = new common_node();

        $Taxonomy = new common_taxonomy();

        $node_data = $Node->nodeDetail($node_id);
        
        $this->tpl->assign('COMPETITION', $Taxonomy->labelDetail($node_data['component']['competition']));
        $this->tpl->assign('ROUND', $Taxonomy->labelDetail($node_data['component']['round']));
        $this->tpl->assign('VENUE', $Taxonomy->labelDetail($node_data['component']['venue']));
        
        $home_team = $Taxonomy->labelDetail($node_data['component']['home_team']);
        $this->tpl->assign('HOME_TEAM', $home_team);
        if (is_array($home_team['image']) && count($home_team['image']) > 0) $this->tpl->parse('content.home_team_image');
        
        $away_team = $Taxonomy->labelDetail($node_data['component']['away_team']);
        $this->tpl->assign('AWAY_TEAM', $away_team);
        if (is_array($away_team['image']) && count($away_team['image']) > 0) $this->tpl->parse('content.away_team_image');
        
        if (trim($node_data['component']['action']['title']) == "") $node_data['component']['action']['title'] = "Buy Tickets";
        
        $this->tpl->assign("NODE", $node_data);

        if (trim($node_data['component']['action']['url'])) $this->tpl->parse('content.action');
        
        return true;
    }
    
}
