<?php // content="text/plain; charset=utf-8"
//require_once $_SERVER['DOCUMENT_ROOT'] . '/home/pi/jpgraph/src/jpgraph.php';
//require_once $_SERVER['DOCUMENT_ROOT'] . '/home/pi/jpgraph/src/jpgraph_line.php';

// Set the environment variable for GD
putenv('GDFONTPATH=' . realpath('.'));

require_once ('jpgraph/src/jpgraph.php');
require_once ('jpgraph/src/jpgraph_line.php');

// Graph size
$width = 800;
$heigth = 600;


$search = "SELECT * FROM sensors WHERE id > (SELECT MAX(id) FROM sensors) - 20;";
$result = mysql_query($search);

//$datay1 = array(20,15,23,15);
//$datay2 = array(12,9,42,8);
//$datay3 = array(5,17,32,24);
$temper = [];
$humid = [];
$dewP = [];
$time  = [];
for($i = 0; $i < 20; $i++)
{
    $new_table = mysql_fetch_array($result);

    $temper[$i] = $new_table['temperature'];
    $humid[$i] = $new_table['humidity'];
    $dewP[$i] = $new_table['dewPoint'];

    if($i == 2 || $i == 10 || $i == 18)
    {
        $time[$i]  = $new_table['timestamp'];
    }
    else
    {
        $time[$i] = '';
    }
}

mysql_close($connection);

// Setup the graph
$graph = new Graph($width,$heigth);
$graph->SetScale("textlin");

$theme_class=new UniversalTheme;

$graph->SetTheme($theme_class);
$graph->img->SetAntiAliasing(false);
$graph->title->Set('Filled Y-grid');
$graph->SetBox(false);

$graph->img->SetAntiAliasing();

$graph->yaxis->HideZeroLabel();
$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false,false);

$graph->xgrid->Show();
$graph->xgrid->SetLineStyle("solid");
//$graph->xaxis->SetTickLabels(array('A','B','C','D'));
$graph->xaxis->SetTickLabels($time);
$graph->xgrid->SetColor('#E3E3E3');

// Create the first line
//$p1 = new LinePlot($datay1);
$p1 = new LinePlot($temper);
$graph->Add($p1);
//$p1->SetColor("#6495ED");
$p1->SetColor("#FF0000");
$p1->SetLegend('Temperature');

// Create the second line
//$p2 = new LinePlot($datay2);
$p2 = new LinePlot($humid);
$graph->Add($p2);
//$p2->SetColor("#B22222");
$p2->SetColor("#0000FF");
$p2->SetLegend('Humidity');

// Create the third line
//$p3 = new LinePlot($datay3);
$p3 = new LinePlot($dewP);
$graph->Add($p3);
//$p3->SetColor("#FF1493");
$p3->SetColor("#00FF00");
$p3->SetLegend('Dew point');

$graph->legend->SetFrameWeight(1);

// Output line
$graph->Stroke();

?>


