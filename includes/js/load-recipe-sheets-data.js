jQuery(document).ready(function () {
  // console.log("good");
  jQuery("#run-import-recipe-sheets").length && tcLoadRunSheetsButton();
});

const tcRunImportSheetsData = () => {
  jQuery("#results").html("Importing recipe data..");
  jQuery.ajax({
    url: recipeSheets.ajaxurl,
    type: "POST",
    datatype: "html",
    data: {
      action: "import_recipe_sheets",
      security: recipeSheets.security,
    },
    success: function (responseText) {
      console.log(responseText);
      //const parseResponse = JSON.parse(responseText);
      jQuery("#results").html(responseText);
    },
    error: function (xhr, status, errorThrown) {
      console.log(errorThrown);
      alert(
        "Error importing sheets data. Your login may have timed out. Please refresh the page and try again."
      );
    },
  });
};

const tcLoadRunSheetsButton = () => {
  jQuery("#run-import-recipe-sheets")
    .off("click")
    .click(function (e) {
      e.preventDefault();
      tcRunImportSheetsData();
    });
};
