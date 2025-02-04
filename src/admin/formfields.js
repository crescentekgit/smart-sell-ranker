/* global SSRLocalizer */
import React, { useState, useEffect } from "react";
import Select from 'react-select';
import axios from "axios";

const FormFields = (props) => {
  const [state, setState] = useState({
    open_model: false,
    datamclist: [],
    from_loading: false,
    errordisplay: ""
  });

  const useQuery = () => {
    return new URLSearchParams(useLocation().hash);
  };

  const onSubmit = (e) => {   
    const prop_submitbutton =
      props.submitbutton && props.submitbutton === "false" ? "" : "true";
    if (prop_submitbutton) {
      e.preventDefault();
    }
    setState((prevState) => ({
      ...prevState,
      from_loading: true,
    }));

    axios({
      method: props.method,
      url: `${SSRLocalizer.apiUrl}/${props.url}`,
      headers: { 'X-WP-Nonce' : SSRLocalizer.nonce },
      data: {
        model: state,
        modulename: props.modulename,
      },
    }).then((res) => {
      setState((prevState) => ({
        ...prevState,
        from_loading: false,
        errordisplay: res.data.error,
      }));
      setTimeout(() => {
        setState((prevState) => ({
          ...prevState,
          errordisplay: "",
        }));
      }, 2000);
      if (res.data.redirect_link) {
        window.location.href = res.data.redirect_link;
      }
    });
  };

  useEffect(() => {
    // Fetch all datas
    props.model.map((m) => {
      setState((prevState) => ({
        ...prevState,
        [m.key]: m.database_value,
      }));
    });
  }, []); // Empty dependency array to run once on mount

  const onChange = (e, key, type = "single", from_type = "", array_values = []) => {
    if (type === "single") {
      if (from_type === "select") {
        setState((prevState) => ({
          ...prevState,
          [key]: e,
        }));
      } else if (from_type === "multi-select") {
        setState((prevState) => ({
          ...prevState,
          [key]: e,
        }));
      } else if (from_type === 'checkbox') {
        setState((prevState) => ({
          ...prevState, 
          [key]: e.target.checked 
        }));
       } else {
        setState((prevState) => ({
          ...prevState,
          [key]: e.target.value,
        }));
      }
    } else {
      const found = state[key] ? state[key].find((d) => d === e.target.value) : false;
      if (found) {
        const data = state[key].filter((d) => {
          return d !== found;
        });
        setState((prevState) => ({
          ...prevState,
          [key]: data,
        }));
      } else {
        const others = state[key] ? [...state[key]] : [];
        setState((prevState) => ({
          ...prevState,
          [key]: [e.target.value, ...others],
        }));
      }
    }
    if (props.submitbutton && props.submitbutton === "false") {
      if (key !== "password") {
        setTimeout(() => {
          onSubmit("");
        }, 10);
      }
    }
  };

  const renderForm = () => {
    const model = props.model;
    const formUI = model.map((m, index) => {
      const key = m.key;
      const type = m.type || "text";
      const props = m.props || {};
      const name = m.name;
      let value = m.value;
      const placeholder = m.placeholder;
      const limit = m.limit;
      let input = "";

      const target = key;

      value = state[target] || "";

      if (m.restricted_page && m.restricted_page === useQuery().get("tab")) {
        return false;
      }

      // If no array key found
      if (!m.key) {
        return false;
      }

      // for select selection
      if (
        m.depend &&
        state[m.depend] &&
        state[m.depend].value &&
        state[m.depend].value !== m.dependvalue
      ) {
        return false;
      }

      // for radio button selection
      if (
        m.depend &&
        state[m.depend] &&
        !state[m.depend].value &&
        state[m.depend] !== m.dependvalue
      ) {
        return false;
      }

      // for checkbox selection
      if (
        m.depend_checkbox &&
        state[m.depend_checkbox] &&
        state[m.depend_checkbox].length === 0
      ) {
        return false;
      }

      // for checkbox selection
      if (
        m.not_depend_checkbox &&
        state[m.not_depend_checkbox] &&
        state[m.not_depend_checkbox].length > 0
      ) {
        return false;
      }

      if (m.depend && !state[m.depend]) {
        return false;
      }

      if (type === "text" || "url" || "password" || "email" || "number") {
        input = (
          <div className="ssp-settings-basic-input-class">
            <input
              {...props}
              className="ssp-setting-form-input"
              type={type}
              key={key}
              id={m.id}
              placeholder={placeholder}
              name={name}
              value={value}
              onChange={(e) => {
                onChange(e, target);
              }}
            />
            {m.desc ? (
              <p
                className="ssp-settings-metabox-description"
                dangerouslySetInnerHTML={{ __html: m.desc }}
              ></p>
            ) : (
              ""
            )}
          </div>
        );
      }

      if (type === "section") {
        input = <div className="ssp-setting-section-divider">&nbsp;</div>;
      }

      if (type === "heading") {
        input = (
          <div className="ssp-setting-section-header">
            {m.blocktext ? (
              <h5
                dangerouslySetInnerHTML={{
                  __html: m.blocktext,
                }}
              ></h5>
            ) : (
              ""
            )}
          </div>
        );
      }

      if (type === "color") {
        input = (
          <div className="ssp-settings-color-picker-parent-class">
            <input
              {...props}
              className="ssp-setting-color-picker"
              type={type}
              key={key}
              id={m.id}
              name={name}
              value={value}
              onChange={(e) => {
                onChange(e, target);
              }}
            />
            {m.desc ? (
              <p
                className="ssp-settings-metabox-description"
                dangerouslySetInnerHTML={{ __html: m.desc }}
              ></p>
            ) : (
              ""
            )}
          </div>
        );
      }

      if (type === "select") {
        const multiarray = [];
				input = m.options.map((o, index) => {
					multiarray[index] = {
						value: o.value,
						label: o.label,
						index,
					};
				});
				input = (
					<div className="ssp-settings-from-multi-select">
						<Select
							className={key}
							value={value}
							options={multiarray}
							onChange={(e) => {
								onChange(
									e,
									m.key,
									'single',
									type,
									multiarray
								);
							}}
						></Select>
						{m.desc ? (
							<p
								className="ssp-settings-metabox-description"
								dangerouslySetInnerHTML={{ __html: m.desc }}
							></p>
						) : (
							''
						)}
					</div>
				);
      }

      if (type === 'multi-select') {
				const multiarray = [];
				input = m.options.map((o, index) => {
					multiarray[index] = {
						value: o.value,
						label: o.label,
						index,
					};
				});
				input = (
					<div className="ssp-settings-from-multi-select">
						<Select
							className={key}
							value={value}
							isMulti
              isClearable='true'
							options={multiarray}
							onChange={(e) => {
								onChange(
									e,
									m.key,
									'single',
									type,
									multiarray
								);
							}}
						></Select>
						{m.desc ? (
							<p
								className="ssp-settings-metabox-description"
								dangerouslySetInnerHTML={{ __html: m.desc }}
							></p>
						) : (
							''
						)}
					</div>
				);
			}

      if (type === 'table') {
				const inputlabels = m.label_options.map((ol) => {
					return <th className="ssp-settings-th-wrap">{ol}</th>;
				});

				input = m.options.map((o) => {
					return (
						<tr className="ssp-settings-tr-wrap">
							<td className="ssp-settings-td-wrap">
								<p
									className="ssp-settings-metabox-description"
									dangerouslySetInnerHTML={{
										__html: o.variable,
									}}
								></p>
							</td>
							<td className="ssp-settings-td-wrap">
								{o.description}
							</td>
						</tr>
					);
				});
				input = (
					<div className="ssp-settings-ssp-form-table">
						<table className="ssp-settings-table-wrap">
							<tr className="ssp-settings-tr-wrap">
								{inputlabels}
							</tr>
							{input}
						</table>
					</div>
				);
			}

      if (type === "textarea") {
        input = (
          <div className="ssp-setting-from-textarea">
            <textarea
              {...props}
              className={m.class ? m.class : "ssp-setting-wpeditor-class"}
              key={key}
              id={m.id}
              placeholder={placeholder}
              name={name}
              value={value}
              onChange={(e) => {
                onChange(e, target );
              }}
            />
            {m.desc ? (
              <p
                className="ssp-settings-metabox-description"
                dangerouslySetInnerHTML={{ __html: m.desc }}
              ></p>
            ) : (
              ""
            )}
          </div>
        );
      }

      if (type === "checkbox") {
        input = (
          <div
            className={
              m.right_content
                ? "ssp-checkbox-list-side-by-side"
                : m.parent_class
                ? "ssp-checkbox-list-side-by-side"
                : ""
            }
          >
            {m.select_deselect ? (
              <div
                className="ssp-select-deselect-trigger"
                onClick={(e) => {
                  this.onSelectDeselectChange(e, m);
                }}
              >
                Select / Deselect All
              </div>
            ) : (
              ""
            )}
            {m.options.map((o) => {
              //let checked = o.value === value;
              let checked = false;
              if (value && value.length > 0) {
                checked = value.indexOf(o.value) > -1 ? true : false;
              }
              return (
                <div
                  className={
                    m.right_content
                      ? "ssp-toggle-checkbox-header"
                      : m.parent_class
                      ? m.parent_class
                      : ""
                  }
                >
                  <React.Fragment key={"cfr" + o.key}>
                    {m.right_content ? (
                      <p
                        className="ssp-settings-metabox-description"
                        dangerouslySetInnerHTML={{
                          __html: o.label,
                        }}
                      ></p>
                    ) : (
                      ""
                    )}
                    <div className="ssp-toggle-checkbox-content">
                      <input
                        {...props}
                        className={m.class}
                        type={type}
                        id={`ssp-toggle-switch-${o.key}`}
                        key={o.key}
                        name={o.name}
                        checked={checked}
                        value={o.value}
                        onChange={(e) => {
                          onChange(e, target, 'checkbox');
                        }}
                      />
                      <label htmlFor={`ssp-toggle-switch-${o.key}`}></label>
                    </div>
                    {m.right_content ? (
                      ""
                    ) : (
                      <p
                        className="ssp-settings-metabox-description"
                        dangerouslySetInnerHTML={{
                          __html: o.label,
                        }}
                      ></p>
                    )}
                    {o.hints ? (
                      <span className="dashicons dashicons-info">
                        <div className="ssp-hover-tooltip">{o.hints}</div>
                      </span>
                    ) : (
                      ""
                    )}
                  </React.Fragment>
                </div>
              );
            })}
            {m.desc ? (
              <p
                className="ssp-settings-metabox-description"
                dangerouslySetInnerHTML={{ __html: m.desc }}
              ></p>
            ) : (
              ""
            )}
          </div>
        );
      }

      if (type === "radio") {
        input = (
          <div className="ssp-settings-basic-input-class ssp-radio-wrapper">
            {props.options &&
              props.options.map((option, i) => (
                <div key={i} className="ssp-radio-option">
                  <input
                    {...props}
                    className="ssp-setting-form-radio"
                    type={type}
                    key={key}
                    id={m.id + i}
                    name={name}
                    value={option.value}
                    checked={value === option.value}
                    onChange={(e) => {
                      onChange(e, target);
                    }}
                  />
                  <label htmlFor={m.id + i} className="ssp-settings-radio-label">
                    {option.label}
                  </label>
                </div>
              ))}
            {m.desc ? (
              <p
                className="ssp-settings-metabox-description"
                dangerouslySetInnerHTML={{ __html: m.desc }}
              ></p>
            ) : (
              ""
            )}
          </div>
        );
      }

      return (
        m.type === "section" || m.label === "no_label" || m.type === "customize_table" ? (
          input
        ) : (
          <div key={"g" + key} className="ssp-form-group">
            <label
              className="ssp-settings-form-label"
              key={"l" + key}
              htmlFor={key}
            >
              <p dangerouslySetInnerHTML={{ __html: m.label }}></p>
            </label>
            <div className="ssp-settings-input-content">{input}</div>
          </div>
        )
      );
    });
    return formUI;
  };

  return (
    <div className="ssp-dynamic-fields-wrapper">
        {state.errordisplay ? (
          <div className="ssp-notice-display-title">
            <i className="ssp-stock-notifier icon-success-notification"></i>
            {state.errordisplay}
          </div>
        ) : (
          ""
        )}
      <form
        onSubmit={(e) => {
          onSubmit(e);
        }}
        className="ssp-dynamic-form"
      >
        <div className="ssp-submit-form">
          <input type="submit" value="Save" class="ssp-button submit-btn " />
        </div>
        {renderForm()}
      </form>
    </div>
  );
};

export default FormFields;