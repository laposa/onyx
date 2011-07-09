<?php
/** 
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Orders_Graph extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		require_once('models/ecommerce/ecommerce_order.php');
		$Order = new ecommerce_order();
		
		//SELECT o.id, b.created FROM ecommerce_order o, ecommerce_basket b WHERE o.basket_id = b.id ORDER BY b.created
		
		if ($_POST['time_frame'] != '') $time_frame = $_POST['time_frame'];
		else $time_frame = 'day';
		$this->tpl->assign("SELECTED_$time_frame", "selected='selected'");
		$this->tpl->assign("TIME_FRAME", $time_frame);
		
		$order_stat = $Order->getStatData($time_frame);
		
		if (is_array($order_stat)) {
			if (count($order_stat) > 0) {
				foreach ($order_stat as $k=>$o) {
					$datax[] = "{$k}";
					$datay[] = $o['success'];
					$datay1[] = $o['unfinished'];
				}
			} else {
				$datax[] = 0;
				$datay[] = 0;
				$datay1[] = 0;
			}
		
			$graph_files = array(
				'orders'=> array( 'title' => "All Orders",
								'x-title' => $time_frame,
								'y-title' => 'The number of orders',
								'file' => ONXSHOP_PROJECT_DIR . "var/cache/graph-success_orders-$time_frame.png"
								)
			);
			$this->tpl->assign('GRAPH', $graph);
		
		
		
		
			include (ONXSHOP_DIR . "lib/jpgraph/jpgraph.php");
			include (ONXSHOP_DIR . "lib/jpgraph/jpgraph_bar.php");
		
			// Create the graph. These two calls are always required
			$graph = new Graph(1000, 600, "auto");
			$graph->ClearTheme();
 
			$graph->SetScale("textlin");
			$graph->yaxis->scale->SetGrace(5);
		
			// Add some grace to y-axis so the bars doesn't go
			// all the way to the end of the plot area
			$graph->xaxis->SetTickLabels($datax);
			$graph->xaxis->SetLabelAngle(50);
			
			// Adjust the margin a bit to make more room for titles
			$graph->img->SetMargin(40,30,20,40);
		
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

			// Display the graph
			$graph->Stroke($graph_files['orders']['file']);
		}

		return true;
	}
}
