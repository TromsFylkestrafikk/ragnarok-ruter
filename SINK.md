# Sink Ruter

This contains transaction data for the old 'Troms Billett' app
develped by Ruter as part of the now dead KK 1.0 collaboration.

The transaction in this app stood for the majority of our
ticketing/product income in the period it was live.

## Data

The data set primarly consist of purchases from the old 'Troms
Billett' app (terminated in september, 2024) and with it all possible
meta data related to phone, app, product and its transactions. This is
located in the `ruter_transactions` table.

As it is possible to buy several tickets in one transaction, an
additional table `ruter_passengers` contains information about all
passengers and the product they has attached to the given purchase.

Historically this data set has been unstable and prone to
changes/errors and unannounced updates.

Another thing to notice is the user identifier. There is no such. The
only way to track users is by app instance name, which changes every
time the app is installed.

## Source

Ruter was the main motivator for the kollektivkameratene 1.0
collaboration, and Troms was for many years the sole PTA using the
platform Ruter developed as part of KK 1.0.

## Usage

This data can be used to identify mobile platform among our user base
and identify big spenders. It can also be used to detect/point out
time/periods with outage to give us an estimate of lost income. As
this is the major source of income for Troms' part the business value
is obvious.
