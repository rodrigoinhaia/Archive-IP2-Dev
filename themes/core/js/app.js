"use strict";

/*
 * Begin of functions
 */

// Insert text into textarea at Caret Position
function insert_at_caret(areaId, text) {
    var txtarea = document.getElementById(areaId),
        scrollPos = txtarea.scrollTop,
        strPos = 0,
        br = ((txtarea.selectionStart || txtarea.selectionStart === "0") ?
            "ff" : (document.selection ? "ie" : false)),
        range;

    if (br === "ie") {
        txtarea.focus();
        range = document.selection.createRange();
        range.moveStart("character", -txtarea.value.length);
        strPos = range.text.length;
    } else if (br === "ff") {
        strPos = txtarea.selectionStart;
    }

    var front = (txtarea.value).substring(0, strPos),
        back = (txtarea.value).substring(strPos, txtarea.value.length);

    txtarea.value = front + text + back;
    strPos = strPos + text.length;
    if (br === "ie") {
        txtarea.focus();
        range = document.selection.createRange();
        range.moveStart("character", -txtarea.value.length);
        range.moveStart("character", strPos);
        range.moveEnd("character", 0);
        range.select();
    } else if (br === "ff") {
        txtarea.selectionStart = strPos;
        txtarea.selectionEnd = strPos;
        txtarea.focus();
    }
    txtarea.scrollTop = scrollPos;
}

function update_email_template_preview() {
    $("#email-template-preview").contents().find("body").html($(".email-template-body").val());
}

// Insert HTML tags into textarea
function insert_html_tag(tag_type, destination_id) {
    var text, sel, text_area, selectedText, startPos, endPos, replace, replaceText, len;
    switch (tag_type) {
        case "text-bold":
            text = ["<b>", "</b>"];
            break;
        case "text-italic":
            text = ["<em>", "</em>"];
            break;
        case "text-paragraph":
            text = ["<p>", "</p>"];
            break;

        case "text-h1":
            text = ["<h1>", "</h1>"];
            break;
        case "text-h2":
            text = ["<h2>", "</h2>"];
            break;
        case "text-h3":
            text = ["<h3>", "</h3>"];
            break;
        case "text-h4":
            text = ["<h4>", "</h4>"];
            break;

        case "text-code":
            text = ["<code>", "</code>"];
            break;
        case "text-hr":
            text = ["<hr/>", ""];
            break;
        case "text-css":
            text = ["<style></style>", ""];
            break;
    }

    // Get the selected text
    text_area = document.getElementById(destination_id);
    if (document.selection !== undefined) {
        text_area.focus();
        sel = document.selection.createRange();
        selectedText = sel.text;
    }
    else if (text_area.selectionStart !== "") {
        startPos = text_area.selectionStart;
        endPos = text_area.selectionEnd;
        selectedText = text_area.value.substring(startPos, endPos);
    }

    // Check if <style> should be added
    if (tag_type === "text-css") {
        replace = text[0] + "\n\r" + text_area.value;
        $(text_area).val(replace);
        update_email_template_preview();
        return true;
    }

    // Check if there is only one HTML tag
    if (text[1].length === 0) {
        insert_at_caret(destination_id, text[0]);
        update_email_template_preview();
        return true;
    }

    // Check if text is selected, replace it or just insert the tag at cursor position
    if (!selectedText || !selectedText.length) {
        text = text[0] + text[1];
        insert_at_caret(destination_id, text);
        update_email_template_preview();
    } else {
        replaceText = text[0] + selectedText + text[1];
        len = text_area.value.length;
        replace = text_area.value.substring(0, startPos) + replaceText + text_area.value.substring(endPos, len);
        $(text_area).val(replace);
        update_email_template_preview();
    }
}

var delay = (function () {
    var timer = 0;
    return function (callback, ms) {
        clearTimeout(timer);
        timer = setTimeout(callback, ms);
    };
})();

/*
 * Begin of scripts
 */
$(document).ready(function () {

    // Height calculation
    var doc = $(document);
    var docHeight = doc.height();
    var main = $("#main");
    var content = $("#content");
    var mainHeight = docHeight - $(".sidebar-toggle-wrapper").outerHeight();
    var contentHeight = mainHeight - $("#headerbar").outerHeight();

    if (doc.width() < 768 && main.height() < docHeight) {
        main.outerHeight(mainHeight);
    }

    if (content.height() < docHeight) {
        content.outerHeight(contentHeight);
    }

    // Dropdowns {
    var docFold = (docHeight / 3) * 2;
    $("[data-toggle='dropdown']").each(function(){
        var toggle = $(this);
        if (toggle.offset().top > docFold) {
            toggle.parent().find(".dropdown-menu").css("top", "auto").css("bottom", toggle.outerHeight());
        }
    });

    // Tooltips
    $("[data-toggle='tooltip']").tooltip();

    // Loader
    $("*[type='submit'], .show-loader").bind("click", function () {
        $("#loader").fadeIn(200);
        $("#loader-error").delay(10000).fadeIn(200);
    });

    // Sidebar toggle handling
    $(".sidebar-toggle").click(function(e){
        e.preventDefault();
        $("#sidebar").toggleClass("show-sidebar");
    });

    // Handle click event for Email Template Tags insertion
    // Example Usage
    // <a href="#" class="text-tag" data-tag="{{{client_name}}}">Client Name</a>
    var lastTaggableClicked;
    $(".text-tag").bind("click", function () {
        var templateTag = this.getAttribute("data-tag");
        insert_at_caret(lastTaggableClicked.id, templateTag);
        return false;
    });

    // Keep track of the last "taggable" input/textarea
    $(".taggable").on("focus", function () {
        lastTaggableClicked = this;
    });

    // HTML tags to email templates textarea
    $(".html-tag").click(function () {
        var tag_type = $(this).data("tagType");
        var body_id = $(".email-template-body").attr("id");
        insert_html_tag(tag_type, body_id);
    });

    // Email Template Preview handling
    var email_template_body_id = $(".email-template-body").attr("id");

    if ($("#email_template_preview").empty()) {
        update_email_template_preview();
    }

    $(email_template_body_id).bind("input propertychange", function () {
        update_email_template_preview();
    });

    $("#email-template-preview-reload").click(function () {
        update_email_template_preview();
    });

});
