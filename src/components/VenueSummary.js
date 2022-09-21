import React from "react";
import VenueSummaryCard from "./VenueSummaryCard";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
  faTicket,
  faUsers,
  faMoneyBill,
  faCashRegister,
  faCoins,
  faBalanceScale,
} from "@fortawesome/free-solid-svg-icons";

function VenueSummary({ venueObj }) {
  const currency = "€";

  let soldHeading;
  let soldMultiplier;
  switch (venueObj.venue_type) {
    case "Restaurant":
    case "Bar":
      soldHeading = "Total Covers";
      soldMultiplier = 2;
      break;
    case "Hotel":
      soldHeading = "Total People";
      soldMultiplier = 2;
      break;
    case "Product":
    default:
      soldHeading = "Total Products";
      soldMultiplier = 1;
  }

  const redeemedSoldQty = (
    <span>
      <strong>{venueObj.redeemed_qty}</strong> / {venueObj.order_qty}
    </span>
  );

  const financial = (num) => {
    return window.euroLocale.format(Number.parseFloat(num).toFixed(2));
  };

  return (
    <div className="container">
      <div className="row">
        <VenueSummaryCard
          numberDisplay={redeemedSoldQty}
          title={
            <span>
              <strong>Vouchers Redeemed</strong> / Sold
            </span>
          }
          icon={<FontAwesomeIcon icon={faTicket} />}
          iconClass="ticket_icon"
        />
        <VenueSummaryCard
          numberDisplay={venueObj.redeemed_qty * soldMultiplier}
          title={soldHeading}
          icon={<FontAwesomeIcon icon={faUsers} />}
          iconClass="users_icon"
        />
        <VenueSummaryCard
          numberDisplay={`${currency} ${financial(venueObj.gross_revenue)}`}
          title="Gross Revenue"
          icon={<FontAwesomeIcon icon={faMoneyBill} />}
          iconClass="money_bill_icon"
        />
      </div>
      <div className="row">
        <VenueSummaryCard
          numberDisplay={`${currency} ${financial(venueObj.net_payable)}`}
          title="Net Payable"
          icon={<FontAwesomeIcon icon={faCashRegister} />}
          iconClass="cash_register_icon"
        />
        <VenueSummaryCard
          numberDisplay={`${currency} ${financial(venueObj.paid_amount)}`}
          title="Total Payments"
          icon={<FontAwesomeIcon icon={faCoins} />}
          iconClass="coins_icon"
        />
        <VenueSummaryCard
          numberDisplay={`${currency} ${financial(venueObj.balance_due)}`}
          title="Balance Due"
          icon={<FontAwesomeIcon icon={faBalanceScale} />}
          iconClass="balance_scale_icon"
        />
      </div>
    </div>
  );
}

export default VenueSummary;
