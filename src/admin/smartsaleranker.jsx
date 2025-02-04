import React from "react";
import { useLocation } from "react-router-dom";
import SSRTab from "./tabs";

const SmartSaleRanker = () => {
  const useQuery = () => new URLSearchParams(useLocation().hash);

  const SmartSaleRanker = () => {
    const $ = jQuery;
    const menuRoot = $("#toplevel_page_" + "smart-sale-ranker-setting");

    const currentUrl = window.location.href;
    const currentPath = currentUrl.substr(currentUrl.indexOf("admin.php"));

    menuRoot.on("click", "a", function () {
      const self = $(this);
      $("ul.wp-submenu li", menuRoot).removeClass("current");
      if (self.hasClass("wp-has-submenu")) {
        $("li.wp-first-item", menuRoot).addClass("current");
      } else {
        self.parents("li").addClass("current");
      }
    });

    $("ul.wp-submenu a", menuRoot).each(function (index, el) {
      if ($(el).attr("href") === currentPath) {
        $(el).parent().addClass("current");
      } else {
        $(el).parent().removeClass("current");
        if (
          $(el).parent().hasClass("wp-first-item") &&
          currentPath === "admin.php?page=smart-sale-ranker-setting"
        ) {
          $(el).parent().addClass("current");
        }
      }
    });

    const location = useQuery();

    if (location.get("tab") && location.get("tab") === "settings") {
      return (
        <SSRTab
          model="top-sale-settings"
          query_name={location.get("tab")}
          subtab={location.get("subtab")}
          funtion_name={SmartSaleRanker}
        />
      );
    } else {
      return (
        <SSRTab
          model="top-sale-settings"
          query_name="settings"
          subtab="general"
          funtion_name={SmartSaleRanker}
        />
      );
    }
  };

  return (
      SmartSaleRanker()
  );
};

export default SmartSaleRanker;