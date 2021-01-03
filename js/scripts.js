(function ($) {
  "use strict";

  // Add active state to sidbar nav links
  var path = window.location.href; // because the 'href' property of the DOM element is the absolute path
  $("#layoutSidenav_nav .sb-sidenav a.nav-link").each(function () {
    if (this.href === path) {
      console.log(this.href, path);
      $(this).addClass("active");
    }
  });

  const linkGroups = [
    {
      parentId: "cus-flow-links",
      childrenEnds: ["res.php", "checkin-out.php", "pay.php"],
    },
    {
      parentId: "report-links",
      childrenEnds: ["report.php"],
    },
  ];

  const hasChildrenEnd = (end) => path.endsWith(end);

  linkGroups.forEach((group) => {
    if (group.childrenEnds.some(hasChildrenEnd)) {
      document.getElementById(group.parentId).classList.add("active");
    }
  });

  // Toggle the side navigation
  $("#sidebarToggle").on("click", function (e) {
    e.preventDefault();
    $("body").toggleClass("sb-sidenav-toggled");
  });
})(jQuery);

// Call the dataTables jQuery plugin
$(document).ready(function () {
  $("#dataTable").dataTable({
    columnDefs: [
      {
        targets: "action",
        orderable: false,
      },
    ],
  });
});

$(function () {
  $('[data-toggle="popover"]').popover();
});
