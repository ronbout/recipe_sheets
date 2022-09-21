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

  return (
    <div className="container">
      <div className="row">
        <VenueSummaryCard
          numberDisplay={venueObj.redeemed_qty}
          title="Vouchers (SOLD!) Redeemed"
          icon={<FontAwesomeIcon icon={faTicket} />}
          iconClass="ticket_icon"
        />
        <VenueSummaryCard
          numberDisplay={venueObj.redeemed_qty}
          title="Total People???"
          icon={<FontAwesomeIcon icon={faUsers} />}
          iconClass="users_icon"
        />
        <VenueSummaryCard
          numberDisplay={`${currency} ${venueObj.gross_revenue}`}
          title="Gross Revenue"
          icon={<FontAwesomeIcon icon={faMoneyBill} />}
          iconClass="money_bill_icon"
        />
      </div>
      <div className="row">
        <VenueSummaryCard
          numberDisplay={`${currency} ${venueObj.net_payable}`}
          title="Net Payable"
          icon={<FontAwesomeIcon icon={faCashRegister} />}
          iconClass="cash_register_icon"
        />
        <VenueSummaryCard
          numberDisplay={venueObj.paid_amount}
          title="Total Payments"
          icon={<FontAwesomeIcon icon={faCoins} />}
          iconClass="coins_icon"
        />
        <VenueSummaryCard
          numberDisplay={`${currency} ${venueObj.balance_due}`}
          title="Balance Due"
          icon={<FontAwesomeIcon icon={faBalanceScale} />}
          iconClass="balance_scale_icon"
        />
      </div>
    </div>
  );
}

export default VenueSummary;
