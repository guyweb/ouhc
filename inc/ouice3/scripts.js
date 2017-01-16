
/* OU ICE jquery addons ........................................................................................................ */
/* Guy Carberry, Steven Price */

$(document).ready(function(){$(".ou-transcript").hide();$('<p><a href="#" class="ou-toggle">Show transcript</a><p/>').appendTo("div.ou-clip");$('a.ou-toggle').click(function(){$(this).text($(this).text()=='Show transcript'?'Hide transcript':'Show transcript');$(this).parent().parent().next().toggle();return false;$(this).html(text)})
});