<?php
/** 
 * Copyright (c) 2006-2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once (ONXSHOP_DIR . "lib/jpgraph/jpgraph.php");
require_once (ONXSHOP_DIR . "lib/jpgraph/jpgraph_bar.php");

class Onxshop_Controller_Bo_Component_Ecommerce_Orders_Graph extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/ecommerce/ecommerce_order.php');
        $Order = new ecommerce_order();
        
        if ($_POST['time_frame'] != '') $time_frame = $_POST['time_frame'];
        else $time_frame = 'day';
        $this->tpl->assign("SELECTED_$time_frame", "selected='selected'");
        $this->tpl->assign("TIME_FRAME", $time_frame);

        if ($time_frame == 'month') $limit = 24;
        else $limit = 30;
        $data = $Order->getStatData($time_frame, $limit);

        if (is_array($data)) {
            $this->generateNumOrdersGraph($data, $time_frame);
            $this->generateRevenueGraph($data, $time_frame);
        }

        return true;

    }       

    public function generateNumOrdersGraph(&$data, $time_frame)
    {
        if (count($data) > 0) {
            foreach ($data as $k=>$o) {
                $datax[] = "{$k}";
                $datay[] = $o['num_orders_finished'];
                $datay1[] = $o['num_orders_unfinished'];
            }
        } else {
            $datax[] = 0;
            $datay[] = 0;
            $datay1[] = 0;
        }
    
        $graph_files = array(
            'orders'=> array(
                'title' => "Order Figures Report",
                'x-title' => $time_frame,
                'y-title' => "The Number of Orders",
                'file' => ONXSHOP_PROJECT_DIR . "var/cache/graph-num_orders-$time_frame.png"
            )
        );
        $this->tpl->assign('GRAPH', $graph);
    
        // Create the graph. These two calls are always required
        $graph = new Graph(1000, 600, "auto");
        $graph->ClearTheme();

        $graph->SetScale("textlin");
        $graph->yaxis->scale->SetGrace(5);
    
        // Add some grace to y-axis so the bars doesn't go
        // all the way to the end of the plot area
        $graph->xaxis->SetTickLabels($datax);
        $graph->xaxis->SetLabelAngle(50);
        
        // Create a bar pot
        $color = '#61a9f3';
        $bplot = new BarPlot($datay);
        $bplot->value->SetFormat('%0.0f');
        $bplot->value->SetColor($color);
        $bplot->SetColor($color);
        $bplot->SetFillColor($color);
        $bplot->value->Show();
        
        //another
        // Create a bar pot
        $color1 = '#f381b9';
        $bplot1 = new BarPlot($datay1);
        $bplot1->value->SetFormat('%0.0f');
        $bplot1->value->SetColor($color1);
        $bplot1->SetColor($color1);
        $bplot1->SetFillColor($color1);
        $bplot1->value->Show();
        
        
        $gbplot = new GroupBarPlot(array($bplot1, $bplot));
        $graph->Add($gbplot);
    
        // Setup the titles
        $graph->title->Set($graph_files['orders']['title']);
        $graph->xaxis->title->Set($graph_files['orders']['x-title']);
        $graph->yaxis->title->Set($graph_files['orders']['y-title']);

        switch ($time_frame) {
            case 'month':
                $graph->yaxis->SetTitleMargin(45);
                $graph->img->SetMargin(65, 30, 20, 40);
                $bplot->value->SetFont(FF_DEFAULT, FS_NORMAL, 7);
                $bplot1->value->SetFont(FF_DEFAULT, FS_NORMAL, 7);
                break;
            case 'week':
                $graph->yaxis->SetTitleMargin(40);
                $graph->img->SetMargin(65, 30, 20, 40);
                $bplot->value->SetFont(FF_DEFAULT, FS_NORMAL, 8);
                $bplot1->value->SetFont(FF_DEFAULT, FS_NORMAL, 8);
                break;
            case 'day':
            default:
                $graph->yaxis->SetTitleMargin(40);
                $graph->img->SetMargin(60, 30, 20, 40);
                $bplot->value->SetFont(FF_DEFAULT, FS_NORMAL, 8);
                $bplot1->value->SetFont(FF_DEFAULT, FS_NORMAL, 8);
                break;
        }

        // Display the graph
        $graph->Stroke($graph_files['orders']['file']);
    }

    public function generateRevenueGraph(&$data, $time_frame)
    {
        if (count($data) > 0) {
            foreach ($data as $k=>$o) {
                $datax[] = "{$k}";
                $datay[] = $o['revenue'];
            }
        } else {
            $datax[] = 0;
            $datay[] = 0;
        }
    
        $graph_files = array(
            'orders'=> array(
                'title' => "Revenue Figures Report",
                'x-title' => $time_frame,
                'y-title' => "Revenue",
                'file' => ONXSHOP_PROJECT_DIR . "var/cache/graph-revenue-$time_frame.png"
            )
        );
        $this->tpl->assign('GRAPH', $graph);
    
        // Create the graph. These two calls are always required
        $graph = new Graph(1000, 600, "auto");
        $graph->ClearTheme();

        $graph->SetScale("textlin");
        $graph->yaxis->scale->SetGrace(5);

        // Add some grace to y-axis so the bars doesn't go
        // all the way to the end of the plot area
        $graph->xaxis->SetTickLabels($datax);
        $graph->xaxis->SetLabelAngle(50);
        
        // Create a bar pot
        $color = '#61a9f3';
        $bplot = new BarPlot($datay);
        $bplot->value->SetFormat('£%0.0f');
        $bplot->value->SetColor('#3968a4');
        $bplot->SetColor($color);
        $bplot->SetFillColor($color);
        $bplot->value->Show();
        
        $gbplot = new GroupBarPlot(array($bplot));
        $graph->Add($gbplot);
    
        // Setup the titles
        $graph->title->Set($graph_files['orders']['title']);
        $graph->xaxis->title->Set($graph_files['orders']['x-title']);
        $graph->yaxis->title->Set($graph_files['orders']['y-title']);

        switch ($time_frame) {
            case 'month':
                $graph->yaxis->SetTitleMargin(45);
                $graph->img->SetMargin(65, 30, 20, 40);
                $bplot->value->SetFont(FF_DEFAULT, FS_NORMAL, 7);
                break;
            case 'week':
                $graph->yaxis->SetTitleMargin(40);
                $graph->img->SetMargin(65, 30, 20, 40);
                $bplot->value->SetFont(FF_DEFAULT, FS_NORMAL, 8);
                break;
            case 'day':
            default:
                $graph->yaxis->SetTitleMargin(40);
                $graph->img->SetMargin(60, 30, 20, 40);
                $bplot->value->SetFont(FF_DEFAULT, FS_NORMAL, 8);
                break;
        }

        // Display the graph
        $graph->Stroke($graph_files['orders']['file']);
    }

}
