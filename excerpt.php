<?php
/**
 * Excerpt
 * 
 * Creates intro excerpts from long passages of text like a Ninja.
 *
 * @category 	snippet
 * @version 	1.0
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 * @autohor 	Kevin Marvin, Ryan Thrash
 * @internal	@modx_category Content
 */
 
/* Description:
 *   Creates shortened blocks of text intelligently, by making 
 *   sure it doesn't break mid-word or in the middle of a tag,  
 *   etc. Also closes any open tags so your layout stays intact.
 */


// Where do you want it to break, roughly?
if (!isset($options)) $options = 1000;
$charcount = $options;
 
// The "continued..." character or text
if (!isset($pad)) $pad = " &hellip;";
 
/*
TODO:
 Figure out how to sanely strip tags to figure out the real character count,
 to prevent premature truncation. For instance if your text has lots of
 markup and hidden stuff in the code, it'll return too short, making you look
 like a rookie. And that's just not good. 
 
 One approach: look at the result, strip out tags and hidden bits and make 
 sure it's long enough. If not, add some more to $charcount, and repeat.
 
 Also make $pad able to be passed in as the options since this is a filter.
*/
 
$output = $input;
 
if(false !== ($breakpoint = strpos($output, " ", $charcount))) { 
	if($breakpoint < strlen($output) - 1) { 
		$output = substr($output, 0, $breakpoint) . $pad; 
	} 
} 
 
$opened = array();
 
if(preg_match_all("/<(\/?[a-z]+)>?/i", $output, $matches)) { 
	foreach($matches[1] as $tag) { 
		if(preg_match("/^[a-z]+$/i", $tag, $regs)) { 
			if(strtolower($regs[0]) != 'br' || strtolower($regs[0]) != 'hr') $opened[] = $regs[0]; 
		} elseif(preg_match("/^\/([a-z]+)$/i", $tag, $regs)) { 
			unset($opened[array_pop(array_keys($opened, $regs[1]))]); 
		} 
	} 
}  
if($opened) { 
	$tagstoclose = array_reverse($opened); 
	foreach($tagstoclose as $tag) $output .= "</$tag>"; 
}
 
 
return $output;