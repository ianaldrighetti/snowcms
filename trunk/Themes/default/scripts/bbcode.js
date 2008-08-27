/*
 * The contents of bbcode_mini.js is this file ran through the YUI compressor option of
 * http://compressorrater.thruhere.net/
 */
function add_bbcode(div_id, before, after) {
 textarea = document.getElementById(div_id);
 if (textarea.selection != "undefined" && textarea.createTextRange) {
  was_empty = textarea.selection.text.length;
  textarea.selection.text = textarea.selection.text.charAt(textarea.selection.text.length - 1) == ' ' ? before + textarea.selection.text + after + ' ' : before + textarea.selection.text + after;
  if (was_empty) {
   textarea.selection.moveStart("character", -after.length);
   textarea.selection.moveEnd("character", -after.length);
   textarea.selection.select();
  }
  else
   textarea.focus(textarea.selection);
 }
 else if (textarea.selectionStart != "undefined") {
  str = textarea.value.substring(0,textarea.selectionStart);
  str += before;
  str += textarea.value.substring(textarea.selectionStart,textarea.selectionEnd);
  str += after;
  str += textarea.value.substring(textarea.selectionEnd);
  textarea.value = str;
 }
 else {
  textarea.value += before + after;
  textarea.focus(textarea.value.length - 1);
 }
}