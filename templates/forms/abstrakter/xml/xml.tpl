<?xml version="1.0" encoding="UTF-8"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x="urn:schemas-microsoft-com:office:excel"
xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">
	<Styles>
		<Style ss:ID="tucne">
			<Font ss:Bold="1" />
		</Style>
	</Styles>
	
	<Worksheet ss:Name="{$worksheetTitle}">
	<Table>
	<Column ss:Index="1" ss:AutoFitWidth="0" ss:Width="100"/>
	
	<Row>
		{foreach from=$firstRow item=row key=k}
			<Cell ss:StyleID="tucne"><Data ss:Type="String">{$k}</Data></Cell>
		{/foreach}
	
	</Row>
	
	{foreach from=$data item=row key=k}
	<Row>
		{foreach from=$row item=cellData key=rowKey}
			<Cell><Data ss:Type="String"><![CDATA[{$cellData}]]></Data></Cell>
		{/foreach}
	</Row>
	{/foreach}
	
	</Table>
	</Worksheet>
	
</Workbook>