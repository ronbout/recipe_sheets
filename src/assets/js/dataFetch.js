/**
 * dataFetch is a generic fetch function that uses async await
 * and will clean up the fetch routines so that all have a standard
 * code.  It will use try/catch for error handling.
 */

const API_QUERY = "?test=rontest";

export default async function dataFetch(
  endpoint,
  queryStr = "",
  httpMethod = "GET",
  body = null,
  formData = false,
  // urlBase = tasteVenuePortal.apiUrl,
  urlBase = "http://localhost/taste2/wp-json/thetaste/v1/",
  wpFlag = false, // the resume api's return a data object when successful.  wp does not
  wpNonce = ""
) {
  const apiQuery = API_QUERY + (queryStr ? "&" : "");
  let basicUrl = `${urlBase}${endpoint}${apiQuery}${queryStr}`;

  console.log("url: ", basicUrl);
  let headers = {};
  headers = {
    headers: {},
  };
  headers = formData
    ? headers
    : { headers: { ...headers.headers, "Content-Type": "application/json" } };

  if (wpFlag) {
    let wpHeaders = wpNonce
      ? {
          "X-WP-Nonce": wpNonce,
        }
      : {
          Authorization: `Basic ${window.btoa(
            "Ronbout:xid4oN&@g4y9wTAul&Kfirs8"
          )}`,
        };
    headers = {
      headers: {
        ...headers.headers,
        ...wpHeaders,
      },
    };
  }

  let httpConfig = body
    ? {
        method: httpMethod,
        body: formData ? body : JSON.stringify(body),
        ...headers,
      }
    : { ...headers };

  try {
    const response = await fetch(basicUrl, httpConfig);
    let result = await response.json();
    console.log("result", result);
    if (result.error) {
      return result;
    } else {
      const resultData = result;
      result = convertNullsToEmpty(resultData);
      return result;
    }
  } catch (error) {
    console.log("Fetch error: ", error);
    return { error: error };
  }
}

export function convertNullsToEmpty(obj) {
  // obj could be array, object or as single value
  // use recursion to get to the single value and
  // replace any nulls with an empty space.
  // this is for setting state for forms
  if (Array.isArray(obj)) {
    // loop through array and call this function on each elementFromPoint
    return obj.map((val) => convertNullsToEmpty(val));
  }
  if (obj !== null && typeof obj === "object") {
    // since we have already handled arrays, must be actual object
    Object.keys(obj).forEach(
      (key) => (obj[key] = convertNullsToEmpty(obj[key]))
    );
    return obj;
  }
  // to be here, we have a scalar
  return obj !== null ? obj : "";
}
