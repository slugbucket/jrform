<?php
  $x_args = array( '_xml' => $xml, '_xsl' => $xsl, '_result' => $result );
/* Reports suggest that xslt_process won't work with NULL param list */
$x_params = array( '_param' => $params);
$ssheet = xslt_create();
xslt_set_encoding( $ssheet, "UTF-8" );
/* xslt logging options */
xslt_set_log( $ssheet, 4 );
xslt_set_log( $ssheet, "/tmp/xslt.err" );

/* Now generate the XML output file */
$xml_file = "form1.xml";
$result = xslt_process( $ssheet, $xml_file, 'admin_topic.xslt', NULL, $x_args, $x_params );
print $result;

xslt_free( $ssheet );
   
?>
