$(document).ready(function () {
  $("#gardenTable").DataTable({
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json",
    },
    order: [],
    columnDefs: [
      {
        orderable: false,
        targets: [8],
      },
    ],
  });
});

$(document).ready(function () {
  $("#userTable").DataTable({
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json",
    },
    order: [],
    columnDefs: [
      {
        orderable: false,
        targets: [6],
      },
    ],
  });
});
