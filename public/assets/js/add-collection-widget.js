jQuery(document).ready(function () {
    jQuery(".add-another-collection-widget").click(function () {
        let list = jQuery(jQuery(this).attr("data-list-selector"));
        let counter = list.data("widget-counter") || list.children().length;
        let newWidget = list.attr("data-prototype");
        newWidget = newWidget.replace(/__name__/g, counter);
        counter++;
        list.data("widget-counter", counter);
        let newElem = jQuery(list.attr("data-widget-tags")).html(newWidget);
        newElem.appendTo(list);
    });
});