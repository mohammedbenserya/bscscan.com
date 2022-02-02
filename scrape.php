
<?php
if(isset($_POST['nbr'])){
$token = $_POST['token'];
$str ='https://bscscan.com/tokenholdingsHandler.aspx?&a='.$token.'&q=&p=1&f=0&h=0&sort=total_price_usd&order=desc&fav=&langMsg=A%20total%20of%20XX%20tokenSS%20found&langFilter=Filtered%20by%20XX&langFirst=First&langPage=Page%20X%20of%20Y&langLast=Last&ps=25';

$curl = curl_init($str);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

$page = curl_exec($curl);


if(!empty($curl)) { //if any html is actually returned

    $DOM = new DOMDocument;
    libxml_use_internal_errors(true);
    $DOM->loadHTML($page);
    libxml_clear_errors();

    $DOM = new DOMXPath($DOM);

    $pages = $DOM->query("//*[contains(@class, 'page-link text-nowrap')]");
    $pages_nbr= explode(" ", $pages[0]->textContent);
    $pages_nbr=(int)$pages_nbr;
    

}
else {
    print "Not found";
}

$data = []; 
    //if($pages_nbr>1){
    for($i=1;$i<=$pages_nbr;$i++){

    $curl = curl_init('https://bscscan.com/tokenholdingsHandler.aspx?&a='.$token.'&q=&p='.$i.'&f=0&h=0&sort=total_price_usd&order=desc&fav=&langMsg=A%20total%20of%20XX%20tokenSS%20found&langFilter=Filtered%20by%20XX&langFirst=First&langPage=Page%20X%20of%20Y&langLast=Last&ps=25');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    
    $contents = curl_exec($curl);    $DOM = new DOMDocument;
    $DOM->loadHTML($contents);

    $items = $DOM->getElementsByTagName('tr');

    foreach ($items as $node) {
        $child =$node->childNodes;
        if (isset($child[3]->textContent)){
            $data[] = ["Symbole"=>$child[2]->textContent,"Quantity"=>$child[3]->textContent];
}}}

header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="output.csv"');

// do not cache the file
header('Pragma: no-cache');
header('Expires: 0');

// create a file pointer connected to the output stream
$file = fopen('php://output', 'w');
fputcsv($file, array('SYMBOLE', 'QUANTITY'));
// output each row of the data
foreach ($data as $row)
{
fputcsv($file, $row);
}
 
exit();
}