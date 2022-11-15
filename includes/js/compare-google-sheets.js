jQuery(document).ready(function () {
  // console.log("good");
  jQuery("#run-compare-google-sheets").length && tcLoadRunCompareSheetsButton();
});

const tcRunCompareSheets = () => {
  jQuery("#results").html("Loading and Comparing Sheets...");
  jQuery("#run-compare-google-sheets").attr("disabled", true);
  const id1 = jQuery("#sheet1-google-id").val();
  const id2 = jQuery("#sheet2-google-id").val();
  const name1 = jQuery("#sheet1-name").val();
  const name2 = jQuery("#sheet2-name").val();
  const sheetInfo = { id1, id2, name1, name2 };

  jQuery.ajax({
    url: recipeSheets.ajaxurl,
    type: "POST",
    datatype: "html",
    data: {
      action: "compare_google_sheets",
      security: recipeSheets.security,
      sheet_info: sheetInfo,
    },
    success: function (responseText) {
      console.log(responseText);
      //const parseResponse = JSON.parse(responseText);
      jQuery("#results").html(responseText);
      jQuery("#run-compare-google-sheets").attr("disabled", false);
    },
    error: function (xhr, status, errorThrown) {
      console.log(errorThrown);
      alert(
        "Error compariing sheets data. Your login may have timed out. Please refresh the page and try again."
      );
      jQuery("#run-compare-google-sheets").attr("disabled", false);
    },
  });
};

const tcLoadRunCompareSheetsButton = () => {
  jQuery("#run-compare-google-sheets")
    .off("click")
    .click(function (e) {
      e.preventDefault();
      tcRunCompareSheets();
    });

  jQuery(".sheet-input").change(checkRunButtonDisable);
};

const checkRunButtonDisable = () => {
  const id1 = jQuery("#sheet1-google-id").val();
  const id2 = jQuery("#sheet2-google-id").val();
  const name1 = jQuery("#sheet1-name").val();
  const name2 = jQuery("#sheet2-name").val();

  if (id1 && id2 && name1 && name2) {
    jQuery("#run-compare-google-sheets").attr("disabled", false);
  } else {
    jQuery("#run-compare-google-sheets").attr("disabled", true);
  }
};
