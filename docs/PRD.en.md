# Bersuara PRD

## 1. Product summary

Bersuara is a concert ticket website for the Greater Jakarta area. Users can register as buyers, log in, view concerts, buy tickets, make simulated payments, and open digital tickets.

Admins use the same login page as buyers. The system directs admins to the admin page based on their role. Admins can manage concert data. Bersuara is a midterm Web Programming course assignment completed by two people. This is not a real project and does not involve real money.

## 2. Background and problem

Bersuara helps people buy concert tickets without going directly to the purchase location and without using different websites for different concerts.

## 3. Goals and success measures

### Goals

1. Provide a concert ticket website for the Greater Jakarta area.
2. Allow buyers to register, log in, view concerts, buy tickets, and open digital tickets.
3. Allow admins to manage concert data.

### Success measures

1. All test scenarios in this PRD pass without errors.
2. The website runs and can be accessed on localhost.

## 4. Users

### Buyer

A buyer registers through the registration page. A buyer can log in, view concerts, buy tickets, make a simulated payment, and open the order history.

Buyers are typically music enthusiasts and concertgoers in the Greater Jakarta area (aged 17–35) who regularly use smartphones or laptops. They prioritize quick access to concert schedules, straightforward ticket quantity selection, transparent quota availability, and the convenience of paperless digital tickets accessible anytime.

### Admin

An admin account is created directly in the database through phpMyAdmin with an admin role column. An admin does not register through the registration page. An admin can log in through the same login page as a buyer and manage concert data.

Admins are internal staff or managers of the Bersuara platform responsible for maintaining the event catalog. They have fundamental technical understanding to operate data through the web UI, ensure schedule and quota accuracy, and protect the integrity of finalized order histories.

## 5. Scope

### In scope

1. Buyer registration, login, and logout.
2. Concert list, search, and filters.
3. Concert details.
4. Ticket purchase with a limit of four paid tickets per account for each concert.
5. Simulated payment, digital tickets, and order history.
6. Admin login, concert list, adding, editing, and deleting concerts.
7. Admin order list as a Could feature if time allows.
8. Ten sample concert records.
9. Responsive display and basic security.

### Out of scope

1. Sales reports.
2. User account management.
3. Concert poster uploads.
4. Real payment or a payment gateway.
5. Filters other than artist, location, and date.
6. QR codes on digital tickets.
7. Admin registration through the registration page.

## 6. User stories

1. As a buyer, I want to register with my personal information, so that I can use the website as a buyer.
2. As a buyer, I want to log in, so that I can access features that require an account.
3. As a buyer, I want to log out, so that my account session is removed.
4. As a buyer, I want to view and search the concert list and use filters, so that I can find the concert I want.
5. As a buyer, I want to view concert details, so that I can check the information before buying.
6. As a buyer, I want to buy tickets and make a simulated payment, so that I can receive the tickets I need.
7. As a buyer, I want to view the order history, so that I can reopen digital tickets.
8. As an admin, I want to log in through the same page, so that the system directs me based on my role.
9. As an admin, I want to view, add, edit, and delete concerts, so that I can manage concert data.
10. As an admin, I want to view the order list, so that I can view order data without changing it.

## 7. Functional requirements

Priorities: Must, Should, and Could.

### FR-01 Buyer registration, Must

The system provides fields for full name, email, phone number, password, and password confirmation. Registration only creates buyer accounts.

### FR-02 Registration validation, Must

All fields are required. The email format must be valid and not registered. The password must have at least eight characters. The confirmation must match. An error message appears below the incorrect field.

### FR-03 Login and logout, Must

Login uses an email and password. Logout removes the session.

### FR-04 Concert list, Must

The list displays the concert name, artist or band, location, price, concert time, ticket type, and remaining tickets. Concerts whose time has passed remain visible with the label “Selesai” and a darker display.

### FR-05 Search and filters, Should

The system provides artist search with partial matching and suggestions like Spotify, a location filter, and a single-date filter. No other filters are included.

### FR-06 Concert details, Must

The details page displays all concert data.

### FR-07 Purchase access, Must

A user without a login session who presses the buy button is directed to login. If remaining tickets are zero, the buy button is disabled and the label “Habis” is shown. Concerts whose time has passed cannot be purchased.

### FR-08 Purchase limit, Must

A buyer can buy more than one ticket, but no more than four paid tickets per account for each concert. Only paid orders count. An order that exceeds the limit is rejected with a clear message.

### FR-09 Payment page, Must

The page displays the concert name, artist, location, time, price per ticket, quantity, and total price. The name, email, and phone number are filled from the account but can be edited. The quantity must be at least one and cannot exceed remaining tickets. Payment methods are e-wallet or bank.

### FR-10 Simulated payment, Must

The “Bayar” button changes the status from “menunggu pembayaran” to “lunas”. The system does not request a real card number, bank account number, or PIN. After payment is completed, remaining tickets decrease according to the purchase quantity.

### FR-11 Digital ticket, Must

After payment is completed, the ticket displays a unique ticket or order code, buyer name, concert details, ticket quantity, total payment, and status. QR codes are not used.

### FR-12 Order history, Must

A logged-in buyer can reopen digital tickets through the order history.

### FR-13 Admin login and redirection, Must

An admin logs in through the same page and is directed based on the role. Buyers and users without a login session cannot open the admin page.

### FR-14 Admin concert list, Must

An admin views the concert list in a table with edit and delete buttons.

### FR-15 Add a concert, Must

The add form contains the concert name, artist or band, location, price, concert time, ticket type, and initial ticket quantity.

### FR-16 Edit a concert, Must

The edit form is the same as the add form and contains the existing data.

### FR-17 Delete a concert, Must

Deletion requires confirmation. A concert with a paid order cannot be deleted. An admin can only delete a concert without a paid order.

### FR-18 Concert data validation, Must

All fields are required. Price and ticket quantity must be numbers and cannot be negative. Concert time must be a valid date and time.

### FR-19 Admin order list, Could

If completed, an admin can view an order list containing the buyer, concert, ticket quantity, total payment, and status. The page is view-only.

## 8. Business rules

1. **BR-01:** The system uses a first come, first served principle.
2. **BR-02:** Remaining tickets decrease only when payment is completed.
3. **BR-03:** The system checks remaining tickets and the four-ticket limit again when “Bayar” is pressed.
4. **BR-04:** An order is rejected with a clear message when tickets are sold out or insufficient.
5. **BR-05:** An order is rejected with a clear message when the four-ticket limit is exceeded.
6. **BR-06:** A buyer without a login session who presses buy is directed to login.
7. **BR-07:** Zero remaining tickets disable the buy button and show “Habis”.
8. **BR-08:** A concert whose time has passed cannot be purchased, remains visible with “Selesai”, and has a darker display.
9. **BR-09:** Each concert has one ticket type, one price, and one remaining ticket count.
10. **BR-10:** An admin is created directly in the database and does not register through the registration page.

## 9. Non-functional requirements

1. **NFR-01:** The display, buttons, and forms work on phones, tablets, and laptops. Testing uses Developer Tools device mode.
2. **NFR-02:** Passwords are stored as hashes.
3. **NFR-03:** Database queries use prepared statements.
4. **NFR-04:** Displayed data is protected with escaping.
5. **NFR-05:** The payment page, order history, and admin page are protected by a valid session.
6. **NFR-06:** The website runs through Laragon and is accessible on localhost.

## 10. Acceptance criteria

1. **FR-01 through FR-02:** Valid registration creates an account. Empty fields, invalid or registered email, a password shorter than eight characters, and different confirmation are rejected. Errors appear below incorrect fields.
2. **FR-03:** Valid login succeeds, invalid login is rejected, logout removes the session, and protected pages reject invalid sessions.
3. **FR-04 through FR-05:** The list displays the required data. Partial search, artist suggestions, location filtering, and single-date filtering return matching data. A past concert shows “Selesai” with a darker display. A sold-out concert shows “Habis” with a disabled button.
4. **FR-06:** Details display all matching concert data.
5. **FR-07 through FR-08:** A user without a login session is directed to login. Valid quantities can be purchased. Past concerts, sold-out concerts, and purchases exceeding four paid tickets are rejected with a clear message.
6. **FR-09:** The complete summary appears, account data is filled and editable, the quantity is at least one and no greater than remaining tickets, and e-wallet or bank can be selected.
7. **FR-10:** A valid payment changes “menunggu pembayaran” to “lunas”. Real financial data is not requested. Remaining tickets decrease. Checks occur when the button is pressed.
8. **FR-11 through FR-12:** Tickets are available only after payment, have a unique code and required data, can be opened from history, and have no QR code.
9. **FR-13 through FR-18:** An admin can log in and manage concerts. Buyers without the admin role are rejected. Form validation works. The edit form contains existing data. Deletion asks for confirmation and rejects concerts with paid orders.
10. **FR-19:** If completed, the order list displays the five required data items and is view-only.

## 11. Test scenarios

1. **TC-01, FR-01, FR-02:** Submit valid registration and confirm that the account is created.
2. **TC-02, FR-02:** Leave fields empty and confirm that errors appear below them.
3. **TC-03, FR-02:** Use an invalid email and confirm rejection.
4. **TC-04, FR-02:** Use a registered email and confirm rejection.
5. **TC-05, FR-02:** Use a password shorter than eight characters and a different confirmation, then confirm errors.
6. **TC-06, FR-03:** Log in as a buyer, log out, and confirm that the session is removed.
7. **TC-07, FR-03, FR-13:** Without a valid session, open payment, history, and admin pages. Confirm rejection or redirection to login.
8. **TC-08, FR-04:** Open the list and confirm that all concert data is displayed.
9. **TC-09, FR-05:** Enter part of an artist name and confirm results and suggestions.
10. **TC-10, FR-05:** Select a location and one date, then confirm matching results.
11. **TC-11, FR-04, BR-08:** Open a past concert and confirm its label, darker display, and purchase restriction.
12. **TC-12, FR-04, FR-07, BR-07:** Open a concert with zero remaining tickets and confirm “Habis” and a disabled button.
13. **TC-13, FR-06:** Open details and confirm that all data is displayed.
14. **TC-14, FR-07, BR-06:** Press buy without logging in and confirm redirection to login.
15. **TC-15, FR-09:** Complete valid payment data and confirm the summary, account data, and methods.
16. **TC-16, FR-09:** Select a quantity greater than remaining tickets and confirm rejection.
17. **TC-17, FR-10, BR-02:** Pay a valid order and confirm “lunas” and reduced remaining tickets.
18. **TC-18, FR-10:** Confirm that the system does not request a real card, bank account, or PIN.
19. **TC-19, FR-08, BR-05:** Use an account with three paid tickets, try to buy two more, and confirm rejection.
20. **TC-20, FR-10, BR-03, BR-04:** Change availability before payment and confirm that a second check rejects an insufficient order.
21. **TC-21, FR-11:** After payment, open the ticket and confirm the unique code and required data without a QR code.
22. **TC-22, FR-12:** Log in again and confirm that the ticket opens from history.
23. **TC-23, FR-13:** Log in with an admin account and confirm redirection to the admin page.
24. **TC-24, FR-13:** Try the admin page as a buyer and without login, and confirm rejection.
25. **TC-25, FR-14:** Open the admin list and confirm the table and edit and delete buttons.
26. **TC-26, FR-15, FR-18:** Add a concert with valid data and confirm that it is saved.
27. **TC-27, FR-18:** Test empty fields, non-numbers, negative numbers, and invalid time. Confirm rejection.
28. **TC-28, FR-16:** Open edit, confirm existing data, save changes, and check the list.
29. **TC-29, FR-17:** Confirm deletion of a concert without a paid order and confirm removal.
30. **TC-30, FR-17:** Delete a concert with a paid order and confirm rejection.
31. **TC-31, FR-19:** If completed, open the order list and confirm the five data items and view-only access.
32. **TC-32, NFR-01:** Test phone, tablet, and laptop modes. Confirm readable text and usable buttons and forms.
33. **TC-33, NFR-02 through NFR-05:** Check password hashes, prepared statements, escaping, and session protection.
34. **TC-34, NFR-06:** Run through Laragon, open localhost, and confirm that the website works.

## 12. Main user flows

### Buyer

The buyer opens the website, registers, logs in, views the list, searches or filters concerts, opens details, selects a quantity, checks payment, edits data if needed, chooses e-wallet or bank, and presses “Bayar”. The system checks remaining tickets and the four-ticket limit. If successful, the status becomes “lunas”, remaining tickets decrease, and the digital ticket appears. The buyer can reopen it from history after logging in again, then logs out.

### Admin

The admin opens login, signs in with an admin account, and the system checks the role. The admin views, adds, edits, or deletes a concert without a paid order after confirmation. If FR-19 is completed, the admin can view the order list. The admin logs out.

## 13. Technical constraints

1. Languages: HTML, CSS, JavaScript, and PHP.
2. Database: MySQL through phpMyAdmin.
3. Tools: Laragon and VS Code.
4. No framework.
5. The website runs on localhost.
6. The database structure is agreed upon before code is written.
7. Each concert has one ticket type, one price, and one remaining ticket count.
8. The admin is created through phpMyAdmin.
9. Payment is simulated only and QR codes are not used.
10. Ten sample concert records are prepared.

## 14. Assumptions, dependencies, and risks

### Assumptions

The website uses Laragon, the database is available through phpMyAdmin, sample data may be fictional, and an admin account is created before admin testing.

### Dependencies

Laragon, phpMyAdmin, a browser with Developer Tools, an agreed database structure, and Git or one organized shared folder.

### Risks

1. The two team members’ code may not work together if the database structure is not agreed upon.
2. Development time is short and ends on Sunday, October 25, 2026.

## 15. Schedule

1. **Monday through Tuesday:** Final PRD, database structure agreement, registration, and login.
2. **Wednesday through Thursday:** Concert list, details, purchase with simulated payment, digital tickets, and history.
3. **Friday:** Admin add, edit, and delete concerts, plus the order list if time allows.
4. **Saturday:** Testing, bug fixes, responsive checks, and the report.
5. **Sunday:** Finalization and submission.

Deadline: Sunday, October 25, 2026.

## 16. Team workflow

Two people collaborate on all parts without a fixed division. The database structure is agreed upon before code is written. Work uses Git or one organized shared folder. Deliverables are code, the database and its data, and the report. Report content is outside this PRD.

## 17. Open questions

There are no remaining open questions. All functional, non-functional requirements, and user persona descriptions have been agreed upon and completed.

Remaining `[NOT FILLED]` markers: **0**.
