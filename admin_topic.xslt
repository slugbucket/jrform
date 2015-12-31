<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<!-- Start of document -->
<xsl:template match="/">
<html>
<head>
</head>
<body bgcolor="#ffffff">
Topic administration page.
<xsl:apply-templates select="topiclist"/>
</body>
</html>
</xsl:template>

<xsl:template match="topiclist">
<table cellpadding="5" cellspacing="5" border="0">
<tr>
 <th></th><th>Topic</th><th>Owner</th><th>Start Date</th><th>Threads</th><th>Messages</th><th>Permissions</th><th>Approved</th>
</tr>
  <xsl:for-each select="topic">
    <xsl:sort select="topic/@id" data-type="text" order="ascending"/>
      <tr>
            <xsl:attribute name="topic"><xsl:value-of select="@id"/></xsl:attribute>
       <td><a><xsl:attribute name="href">change_topic.php?id=<xsl:value-of select='./@id'/></xsl:attribute>Edit</a></td>
            <xsl:apply-templates/>
      </tr>
  </xsl:for-each>
</table>
</xsl:template>

<xsl:template match="topic">
</xsl:template>

<xsl:template match="title">
        <td><xsl:value-of select="."/></td>
</xsl:template>

<xsl:template match="owner">
        <td><xsl:value-of select="."/></td>
</xsl:template>

<xsl:template match="opened">
        <td><xsl:value-of select="."/></td>
</xsl:template>

<xsl:template match="threads">
        <td><xsl:value-of select="."/></td>
</xsl:template>

<xsl:template match="messages">
        <td><xsl:value-of select="."/></td>
</xsl:template>

<xsl:template match="//perms[@selected='yes']">
<option><xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute><xsl:attribute name="selected"/>
<xsl:value-of select="."/>
</option>
</xsl:template>

<xsl:template match="perms">
<option><xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute>
<xsl:value-of select="."/>
</option>
</xsl:template>

<xsl:template match="permlist">
<td>
<select name="perms" size="1">
  <xsl:apply-templates select="perms"/>
</select>
</td>
</xsl:template>

<xsl:template match="approved">
        <td><xsl:value-of select="."/></td>
</xsl:template>

<!-- End of document -->
</xsl:stylesheet>
