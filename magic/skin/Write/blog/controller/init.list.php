<?php if(!defined("__MAGIC__")) exit; 

$tags = array();
foreach(Tag::Inst()->Sql('group', $this->bo_no) as $v) {
  if($v['cnt']<=1) continue;
  $tags[] = $v['tag_name'];
}
$this->tags = $tags;

// meta keywords
PageElement::Inst('head')->SetConfig('keywords', '', implode(',',$this->tags));

