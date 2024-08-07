# Sink Ruter

This contains transaction data for the old 'Troms Billett' app
develped by Ruter as part of the now dead KK (Kollektivkameratene) 1.0
collaboration.

The transactions in this app stood for the majority of our
ticketing/product income in the period it was live.

## Data

The dataset primarly consist of purchases from the old 'Troms Billett'
app (terminated in September 2024) and with it all possible metadata
related to phone, app, app version, product and its transactions. This
is located in the `ruter_transactions` table.

As it is possible to buy several tickets in one transaction, an
additional table `ruter_passengers` contains information about all
passengers and the product they has attached to the given purchase.

Historically this data set has been unstable and prone to
changes/errors and unannounced updates.

Another thing to notice is the user identifier. There is no such. The
only way to track users is by app instance name, which changes every
time the app is installed.

## Source

Ruter was the main motivator for the KK 1.0 collaboration, and Troms
was for many years the sole Public Transport Authority (PTA) using the
platform Ruter developed as part of KK 1.0.

## Usage

This data can be used to explore usage of the mobile platform among
our user base and identify big spenders. It can also be used to
detect/point out time/periods with outage to give us an estimate of
lost income. This was the major source of income for Troms
Fylkestrafikk (Svipper). Therefore, it has a large business value.
