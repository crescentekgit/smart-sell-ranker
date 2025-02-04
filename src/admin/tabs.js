/* global SSRLocalizer */
import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import axios from "axios";
import FormFields from "./formfields";
import PropagateLoader from "react-spinners/PropagateLoader";


const TabSection = (props) => {
  const [fetchAdminTabs, setFetchAdminTabs] = useState([]);
  const [current, setCurrent] = useState({});
  const [currentUrl, setCurrentUrl] = useState("");

  useEffect(() => {
    if (props.subtab !== currentUrl) {
      axios({
        url: `${SSRLocalizer.apiUrl}/smart_sell_ranker/v1/fetch_admin_tabs`,
        headers: { 'X-WP-Nonce' : SSRLocalizer.nonce },
      }).then((response) => {
        setFetchAdminTabs(
          response.data ? response.data[props.model] : []
        );
        setCurrentUrl(props.subtab);
      });
    }
  }, [props.subtab, props.model, currentUrl]);

  const renderTab = () => {
    const horizontally = props.horizontally;
    const queryName = props.query_name;

    const model = fetchAdminTabs ? fetchAdminTabs : [];

    const TabUI = Object.entries(model).length > 0
      ? Object.entries(model).map((m, index) => {
        return props.subtab === m[0] ? (
          <div className="ssp-tab-description-start" key={index}>
            <div className="ssp-tab-name">{m[1].tablabel}</div>
            <p>{m[1].description}</p>
          </div>
        ) : (
          ""
        );
      })
      : "";

    const TabUIContent = (
      <div className={`ssp-general-wrapper ssp-${props.subtab}`}>
        <div className="ssp-container ssp-tab-banner-wrap">
          <div
            className={`ssp-middle-container-wrapper ${
              horizontally
                ? "ssp-horizontal-tabs"
                : "ssp-vertical-tabs"
            }`}
          >
            <div className="ssp-middle-child-container">
              {props.no_tabs ? (
                ""
              ) : (
                <div className="ssp-current-tab-lists">
                  {Object.entries(model).length > 0
                    ? Object.entries(model).map((m, index) => {
                      return m[1].link ? (
                        <a className={m[1].class} href={m[1].link} key={index}>
                          {m[1].icon ? (
                            <i className={`stock-notifier-icon ${m[1].icon}`}></i>
                          ) : (
                            ""
                          )}
                          {m[1].tablabel}
                        </a>
                      ) : (
                        <Link
                          className={
                            props.subtab === m[0] ? "active-current-tab" : ""
                          }
                          to={`?page=smart-sale-ranker-setting#&tab=${queryName}&subtab=${m[0]}`}
                          key={index}
                        >
                          {m[1].icon ? (
                            <i className={`stock-notifier-icon ${m[1].icon}`}></i>
                          ) : (
                            ""
                          )}
                          {m[1].tablabel}
                        </Link>
                      );
                    })
                    : ""}
                </div>
              )}
              <div className="ssp-tab-content">
                {props.tab_description && props.tab_description === "no"
                  ? ""
                  : TabUI}
                {model &&
                  Object.entries(model).length > 0 &&
                  props.subtab === currentUrl ? (
                    Object.entries(model).map((m, index) =>
                      m[0] === props.subtab &&
                        m[1].modulename &&
                        m[1].modulename.length > 0 ? (
                          <FormFields
                            key={`dynamic-form-${m[0]}`}
                            title={m[1].tablabel}
                            defaultValues={current}
                            model={m[1].modulename}
                            method="post"
                            modulename={m[0]}
                            url={`smart_sell_ranker/v1/${m[1].apiurl}`}
                            submitbutton="true"
                          />
                        ) : (
                          ""
                        )
                    )
                  ) : (
                    <div className="loader_sign">
                      <PropagateLoader color="#e35047" />
                    </div>
                  )}
              </div>
            </div>
          </div>
        </div>
      </div>
    );

    return TabUIContent;
  };

  return renderTab();
};

export default TabSection;