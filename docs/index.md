# The City of Truro Mariners - API Documentation

This will contain all the relevant features and functions of the API if you want code examples see the management console for desktop which uses all of these features

**Notes**: 
* Query Error could be a server error or some data you sent is invalid to see a working example check the desktop app
* All of these are Subject to change however the ones most likely to change are the ones marked as such

## Contents:

* [Login](#login)
* [Main Page (Subject to change)](#mainpagesubjecttochange)
* [Members Page](#memberspage)
* [Adding Members (Subject to change)](#addingmemberssubjecttochange)
* [Deleting Members](#deletingmembers)
* [Editing Members](#editingmembers)
* [Events Ready (Subject to change)](#eventsreadysubjecttochange)
* [Add Events](#addevents)
* [Deleting Events](#deletingevents)
* [Editing Events](#editingevents)
* [Resetting passwords](#resettingpasswords)
* [Loading members for messages](#loadingmembersformessages)
* [Loading messages](#loadingmessages)
* [Sending messages](#sendingmessages)
* [Reading messages](#readingmessagessubjecttochange)
* [Notifying user of new message](#notifyinguserofnewmessage)
* [Adding membership payments (£13)](#addingmembershippayments13)
* [Confirm membership payment](#confirmmembershippayment)
* [Load the membership payment status of the current user](#loadthemembershippaymentstatusofthecurrentuser)
* [Request access/an account](#requestaccessanaccount)
* [Load all outgoing payments/expenditure](#loadalloutgoingpaymentsexpenditure)
* [Add outgoing payments/expenditure](#addoutgoingpaymentsexpenditure)
* [Loading members for accounting drop down](#loadingmembersforaccountingdropdown)
* [Editing Expenditure](#editingexpenditure)
* [Deleting Expenditure](#deletingexpenditure)
* [Load all incoming payments](#loadallincomingpayments)
* [Adding incoming payments (Subject to change)](#addingincomingpaymentssubjecttochange)
* [Editing incoming payments (Subject to change)](#editingincomingpaymentssubjecttochange)
* [Deleting Payment](#deletingpayment)
* [Adding event attending status](#addingeventattendingstatus)
* [Load all events attending for current user](#loadalleventsattendingforcurrentuser)
* [Load all events attending](#loadalleventsattending)
* [Unattend an event (Subject to change)](#unattendaneventsubjecttochange)
* [Changing of username](#changingofusername)
* [Changing of password](#changingofpassword)
* [Changing of email](#changingofemail)

## Login:

### To use login you must send a POST request to the server containing the following values:

* formname 
* username
* password
* datetime

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **login**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

DateTime is used to log when the user accessed the API and should be included using the MySQL datetime format 

### Possible responces:

* 1
* 0
* Incorrect
* Query Error

#### Explanation:

A response of 1 means the user is an admin and should be logged in with elevated views

A response of 0 means the user is standard and should be logged in with standard views

A repsonse of Incorrect means the username/password is incorrect

A reponse of Query Error means some kind of error occured in MySQL when updating the log file

## Main Page (Subject to change):

### To use the main page you must send a POST request to the server containing the following values:

* formname 
* username
* password

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **mainReady**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

### Possible responces:

* JSON array of all usernames
* Unauthorised

#### Explanation:

A JSON array of all usernames means loading main was successful (we display the member count on our main page)

A response of Unauthorised means users identity could not be confirmed the username/password were wrong

## Members Page:

### To use the members page you must send a POST request to the server containing the following values:

* formname 
* username
* password

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **membersReady**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

### Possible responces:

* JSON array of all usernames,Privileges and emails
* Unauthorised

#### Explanation:

A JSON array means loading members was successful
**Note**: This only sends back this response if the username and password are correct and the member accessing it is an admin

A response of Unauthorised means users identity could not be confirmed the username/password were wrong

## Adding Members (Subject to change):

### To add members you must send a POST request to the server containing the following values:

* formname 
* username
* password
* newpassword
* email
* usertype

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **addMember**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

New password is the password for the member being added encrypted using aes-256-cbc with the keys above.

Email is the new users email address.

User type is the new users privilege level can be 1 or 0
**Note**: 1 = Admin, 0 = Standard user

### Possible responces:

* Already Exists
* Mailer Error
* Unauthorised
* Query Error
* Success

#### Explanation:

A response of Already Exists means a user with the same username or email already exists and therefore the user cannot be added

A response of Mailer Error means that the account was **NOT** added to the club and an email was **NOT** sent to the email submitted

A response of Unauthorised means users identity could not be confirmed the username/password were wrong or the account being used to add a new member is **NOT** an admin

A reponse of Query Error means some kind of error occured in MySQL when adding the new member however it means that the email submitted **WAS** sent a new member email (This may be changed to make behaviour better)

A response of Success means the member **WAS** added and the email **WAS** sent

## Deleting Members:

### To delete members you must send a POST request to the server containing the following values:

* formname 
* username
* password
* deleteusername

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **deleteMember**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

Delete username is the user to be deleted
**Note**: Only admins can delete members

### Possible responces:

* Unauthorised
* Query Error

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong or the account being used to delete a member is **NOT** an admin

A reponse of Query Error means some kind of error occured in MySQL when deleting the member this could be a lot of different things e.g. the user to be deleted doesn't exist or some generic server error occured

## Editing Members:

### To edit members you must send a POST request to the server containing the following values:

* formname 
* username
* password
* editemail
* editprivileges
* editusername

#### Explanation:
**Note**: Only admins can edit members

Formname defines what part of the API you want to use so in this case the formname would be **editMember**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

Edit email will be the new email for the member being edited

Edit privileges will be the new privilege level for the member being edited

Edit username is the username of the member you want to edit
**Note**: The username cannot be changed the editusername value should be the username of the existing account

### Possible responces:

* Already Exists
* Unauthorised
* Query Error

#### Explanation:

A response of Already Exists means that another user is already using the email address that is attempting to be changed

A response of Unauthorised means users identity could not be confirmed the username/password were wrong or the account being used to edit a member is **NOT** an admin

A reponse of Query Error means some kind of error occured in MySQL when editing the member this could be a lot of different things e.g. the user to be edited doesn't exist or some generic server error occured

## Events Ready (Subject to change):

### To load the events ready page you must send a POST request to the server containing the following values:

* formname 
* username
* password

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **eventsReady**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

### Possible responces:

* JSON array containing all events
* Unauthorised

#### Explanation:

A JSON array containing all events means it was successfull and those events should be displayed to the user.
**Note**: Currently the API will return all events that exist whether or not they are approved therefore only approved events should be shown to standard users this is subject to change and will be altered in a new version of the API when i get round to it

A response of Unauthorised means users identity could not be confirmed the username/password were wrong

## Add Events:

### To add events you must send a POST request to the server containing the following values:

* formname 
* username
* password
* title
* description
* datetime
* location
* accepted

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **addEvent**

Username is just the account username as well as who submitted the event

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

Title is the title of the event to be added

Description is the description of the event to be added

Datetime is the datetime the event takes place
**Note**: This must be in the MySQL datetime format

Location is the location of the event e.g. Malpas/Boscawen park lake

Accepted is whether the event is approved by an admin
**Note**: Server side verification is done for this value e.g. if your a standard user but approved is true it wont get added as the event should **NOT** be approved if your a standard user
**Note**: 1 = approved, 0 = unapproved

### Possible responces:

* Submitted
* Unauthorised
* Query Error

#### Explanation:

A response of Submitted means that the event was added successfully but as it was submitted by a standard user it means that it is pending approval by an admin 

A response of Unauthorised means users identity could not be confirmed the username/password were wrong or the account being used to add a new member is **NOT** an admin but the approved value was 1

A reponse of Query Error means some kind of error occured in MySQL when adding the event this could be a lot of different things e.g. some generic server error occured


## Deleting Events:

### To delete events you must send a POST request to the server containing the following values:

* formname 
* username
* password
* ID

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **deleteEvent**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

ID is the ID of the event to be deleted
**Note**: Only admins can delete events

### Possible responces:

* Unauthorised
* Query Error

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong or the account being used to delete an event is **NOT** an admin

A reponse of Query Error means some kind of error occured in MySQL when deleting the event this could be a lot of different things e.g. the event to be deleted doesn't exist or some generic server error occured


## Editing Events:

### To edit events you must send a POST request to the server containing the following values:

* formname 
* username
* password
* title
* description
* datetime
* location
* accepted
* ID

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **deleteEvent**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

ID is the ID of the event to be edited
**Note**: Only admins can edit events and the ID cannot be changed

Title is the new title for the event

Description is the new description of the event

Datetime is the new datetime that the event is taking place
**Note**: This must be written in MySQL datetime format

Location is the new location of the event

Accepted is whether or not the event is accepted
**Note**: 1 = accepted, 0 = not accepted

### Possible responces:

* Unauthorised
* Query Error

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong or the account being used to edit an event is **NOT** an admin

A reponse of Query Error means some kind of error occured in MySQL when editing the event this could be a lot of different things e.g. the event to be edited doesn't exist or some generic server error occured

## Resetting passwords:

### Part 1:

#### To reset a password you must send a POST request to the server containing the following values:

* formname 
* email

##### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **resetPassword**

Email is the email address linked to the account

#### Possible responces:

* Success
* No Account
* Query Error
* Mailer Error

##### Explanation:

A response of Success means an email with a password reset code was sent to the member's email if the account was found

A response of No Account means no valid account was found that was linked with the email given

A reponse of Query Error means some kind of error occured in MySQL when resetting the password this could be a lot of different things e.g. some generic server error occured

A response of Mailer Error means that an email was **NOT** sent to the email provided and a reset code was **NOT** generated

### Part 2:

#### To confirm a password reset you must send a POST request to the server containing the following values:

* formname 
* newpass
* code

##### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **resetPasswordConfirmed**

New pass is the users new password for the account that requested the reset

Code is the code recieved by the user in an email to confirm their identity

#### Possible responces:

* Success
* Incorrect
* Query Error

##### Explanation:

A response of Success means the accounts password was changed successfully

A response of Incorrect means the code sent to the API was wrong

A reponse of Query Error means some kind of error occured in MySQL when resetting the password this could be a lot of different things e.g. some generic server error occured


## Loading members for messages:

### To load members for messages you must send a POST request to the server containing the following values:

* formname
* username
* password

#### Explanation:
**Note**: This API is used to load all members in the club except you so that they can be later selected and used to load the message thread between the current user and the user selected.

**Note**: This is one of the most complicated parts of the API i will do my best to explain but if still at a complete loss check the code used in the desktop application or make an issue request

Formname defines what part of the API you want to use so in this case the formname would be **messagesReady**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

### Possible responces:

* Unauthorised
* JSON array containing all the members

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong

A JSON array means your request was successful and the array will contain all the current members of the club


## Loading messages:

### To load messages you must send a POST request to the server containing the following values:

* formname
* username
* password
* contact
* ID

#### Explanation:
**Note**: This is one of the most complicated parts of the API i will do my best to explain but if still at a complete loss check the code used in the desktop application or make an issue request

Formname defines what part of the API you want to use so in this case the formname would be **messagesLoad**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

Contact is who your messaging

ID is the ID of the message you want to load so if you want the latest messages you send ID of 0 otherwise send the ID of the next messages to be loaded (i know that doesnt make much sense for an example see the desktop app code)

### Possible responces:

* Unauthorised
* JSON array containing 50 messages

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong

A JSON array means your request was successful and the array will contain 50 messages. Depending on the ID send will depend on messages that get loaded.



## Sending messages:

### To send messages you must send a POST request to the server containing the following values:

* formname
* username
* password
* contact
* message
* file
* datetime

#### Explanation:
**Note**: This is one of the most complicated parts of the API i will do my best to explain but if still at a complete loss check the code used in the desktop application or make an issue request

Formname defines what part of the API you want to use so in this case the formname would be **messageSend**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

Contact is who your messaging

Message is the value of the actual message e.g. "Hello" or a base64 value containing an image or zip file

File is a boolean value to tell whether it is a file or plaintext message
**Note**: 1 = zip/image, 0 = plaintext

Datetime is the datetime in MySQL format

### Possible responces:

* Unauthorised
* Query Error
* Success

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong

A response of Query Error means some kind of error occured in MySQL when sending the message this could be a lot of different things e.g. some generic server error occured

A response of Success means the message/zip/image was successfully sent


## Reading messages (Subject to change):

### To mark a message as read you must send a POST request to the server containing the following values:

* formname
* username
* password
* ID

#### Explanation:
**Note**: This is one of the most complicated parts of the API i will do my best to explain but if still at a complete loss check the code used in the desktop application or make an issue request

Formname defines what part of the API you want to use so in this case the formname would be **message-read**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

ID is the ID of the message thats being read

### Possible responces:

* Unauthorised
* Query Error

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong

A response of Query Error means some kind of error occured in MySQL when marking the message as read this could be a lot of different things e.g. some generic server error occured


## Notifying user of new message:

### To notify a member of a new message you must send a POST request to the server containing the following values:

* formname
* username
* password

#### Explanation:
**Note**: This is one of the most complicated parts of the API i will do my best to explain but if still at a complete loss check the code used in the desktop application or make an issue request

Formname defines what part of the API you want to use so in this case the formname would be **messagesLoadNotify**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

### Possible responces:

* Unauthorised
* JSON array

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong

A JSON array contains all data about a new message/unread message

## Adding membership payments (£13):

### To add a membership payment you must send a POST request to the server containing the following values:

* formname
* username
* password

#### Explanation:
**Note**: There is only so much this explanation can give it may be better to be reading through the desktop app code at the same time as reading this to help it make more sense if you are still struggling feel free to make and issue

Formname defines what part of the API you want to use so in this case the formname would be **membership-payment**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

### Possible responces:

* Unauthorised
* A string containing the client secret for use with stripe

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong

The Client secret is then used within the front end and sent to stripe as well as being used to log the ID of the payment in the payments table for use later on in the application e.g. viewing the payment online



## Confirm membership payment:

### To confirm a membership payment you must send a POST request to the server containing the following values:

* formname
* username
* password
* ID
* Datetime

#### Explanation:
**Note**: There is only so much this explanation can give it may be better to be reading through the desktop app code at the same time as reading this to help it make more sense

Formname defines what part of the API you want to use so in this case the formname would be **membership-payment-success**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

ID is the ID of the payment you can get this from altering the client_secret using the javascript code below:
```
    ID = clientSecret.split("_", 2)
    ID = ID[0] + "_" + ID[1]
```

Datetime is the datetime the payment was made in MySQL datetime format

### Possible responces:

* Unauthorised
* Success
* Query Error

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong

A response of Success means the payment was registered as successful in the program

A response of Query Error means some kind of error occured in MySQL when confirming the payment this could be a lot of different things e.g. some generic server error occured. However dont't panic this still means the payment was successful it just means that the server failed to add it to the table and you will need to ask an admin to confirm the payment was successful and add it to the table.


## Load the membership payment status of the current user:

### To load the membership payment status of the current user you must send a POST request to the server containing the following values:

* formname
* username
* password

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **payments**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

### Possible responces:

* Unauthorised
* JSON array

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong

A JSON array could be empty if no payments exist for the current user however if its not empty it will contain a maximum of 1 payment value which you can use to check if payment is overdue (payment is due once a year)


## Request access/an account:

### To request access you must send a POST request to the server containing the following values:

* formname
* email

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **request-access**

Email is just an email of the person requesting access to the program

### Possible responces:

* Already Access
* Mailer Error
* Request Confirmed

#### Explanation:

A response of Already Access means an account using this email already exists and therefore they already have access to the program

A response of Mailer Error means that API was unable to send an email requesting access therfore you should try again

A response of Request Confirmed means an email was successfully sent to request access

## Load all outgoing payments/expenditure:

### To load all expenditure you must send a POST request to the server containing the following values:

* formname
* username
* password

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **loadExpenditure**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

### Possible responces:

* Unauthorised
* JSON array

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong or the account being used to load all expenditure is **NOT** an admin

A JSON array will contain all the expenditure of the club
**Note**: Only admins can access expenditure


## Add outgoing payments/expenditure:

### To view expenditure you must send a POST request to the server containing the following values:

* formname
* username
* password
* item
* description
* datetime
* location
* member

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **addOutgoing**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

Item is the name of the item money was spent on

Description is a description of the item and its price etc.

Datetime is when it was purchased

Location is where it was purchased e.g. shop and town/city name

Member is who bought it

### Possible responces:

* Unauthorised
* Query Error
* Success

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong or the account being used to edit an event is **NOT** an admin

A response of Query Error means some kind of error occured in MySQL when adding the expenditure this could be a lot of different things e.g. the user to be deleted doesn't exist or some generic server error occured

A response of Success means that expenditure was successfully added


## Loading members for accounting drop down:

### To load members for accounting drop down you must send a POST request to the server containing the following values:

* formname
* username
* password

#### Explanation:
**Note**: This API is used to load all members in the club including yourself to allow for an admin to select what member the expenditure/payment came from when adding new expenditure/payments

Formname defines what part of the API you want to use so in this case the formname would be **usernamesAccounting**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

### Possible responces:

* Unauthorised
* JSON array containing all the members

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong

A JSON array means your request was successful and the array will contain all the current members of the club



## Editing Expenditure:

### To edit expenditure you must send a POST request to the server containing the following values:

* formname
* username
* password
* item
* description
* datetime
* location
* member
* ID

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **editExpend**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

Item is the name of the item money was spent on

Description is a description of the item and its price etc.

Datetime is when it was purchased

Location is where it was purchased e.g. shop and town/city name

Member is who bought it

ID is the ID of the expenditure being edited

### Possible responces:

* Unauthorised
* Query Error

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong or the account being used to edit the expenditure is **NOT** an admin

A reponse of Query Error means some kind of error occured in MySQL when editing the expenditure this could be a lot of different things e.g. the exependiture to be edited doesn't exist or some generic server error occured

## Deleting Expenditure:

### To delete expenditure you must send a POST request to the server containing the following values:

* formname 
* username
* password
* ID

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **deleteExpend**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

ID is the ID of the expenditure to be deleted
**Note**: Only admins can delete expenditure

### Possible responces:

* Unauthorised
* Query Error

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong or the account being used to delete the expenditure is **NOT** an admin

A reponse of Query Error means some kind of error occured in MySQL when deleting the expenditure this could be a lot of different things e.g. the expenditure to be deleted doesn't exist or some generic server error occured


## Load all incoming payments:

### To load all payments you must send a POST request to the server containing the following values:

* formname
* username
* password

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **loadPayments**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

### Possible responces:

* Unauthorised
* JSON array

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong or the account being used to load all incoming payments is **NOT** an admin

A JSON array will contain all the incoming payments of the club
**Note**: Only admins can access incoming payments


## Adding incoming payments (Subject to change):

### To add events you must send a POST request to the server containing the following values:

* formname 
* username
* password
* datetime
* inperson
* type
* memberusername
* amount
* description

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **addPayment**

Username is just the account username as well as who submitted the event

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

Datetime is the datetime the event takes place
**Note**: This must be in the MySQL datetime format

Inperson is whether or not the payment was handled in person or via something like stripe/bank transfer etc.
**Note**: 1 = in person, 0 not in person

Type is what kind of payment is being used the following are valid:

* Donation
* Membership Payment
* Other
**Note**: Membership Payment should be sent with the amount of £13.00

Memberusername is the username of the member who the payment is from

Amount is the cost e.g. £36

Description is the description of the payment to be added e.g. what was it for if other

### Possible responces:

* Success
* Unauthorised
* Query Error

#### Explanation:

A response of Success means that the payment was successfully added to the database

A response of Unauthorised means users identity could not be confirmed the username/password were wrong or the account being used to add a new payment is **NOT** an admin

A reponse of Query Error means some kind of error occured in MySQL when adding the event this could be a lot of different things e.g. some generic server error occured


## Editing incoming payments (Subject to change):

### To edit incoming payments you must send a POST request to the server containing the following values:

* formname
* username
* password
* description
* datetime
* inPerson
* amount
* ID

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **editPayment**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

Description is the description of the payment to be added e.g. what was it for if other

Datetime is the datetime the event takes place
**Note**: This must be in the MySQL datetime format

Inperson is whether or not the payment was handled in person or via something like stripe/bank transfer etc.
**Note**: 1 = in person, 0 not in person

Amount is the cost e.g. £36

ID is the ID of the payment to be edited

### Possible responces:

* Unauthorised
* Query Error

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong or the account being used to edit the payment is **NOT** an admin

A reponse of Query Error means some kind of error occured in MySQL when editing the payment this could be a lot of different things e.g. the payment to be edited doesn't exist or some generic server error occured

## Deleting Payment:

### To delete a payment you must send a POST request to the server containing the following values:

* formname 
* username
* password
* ID

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **deletePayment**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

ID is the ID of the payment to be deleted
**Note**: Only admins can delete payments

### Possible responces:

* Unauthorised
* Query Error
* Failed

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong or the account being used to delete the payment is **NOT** an admin

A reponse of Query Error means some kind of error occured in MySQL when deleting the payment this could be a lot of different things e.g. the payment to be deleted doesn't exist or some generic server error occured

A response of Failed means stripe failed to refund the payment so it is still in the table this could mean that the payment has already been refunded in which case open an issue request with the ID and I will confirm/fix the issue


## Adding event attending status:

### To add an attending status to an event you must send a POST request to the server containing the following values:

* formname 
* username
* password
* id
* title

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **attendEvent**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

ID is the ID of the event to be attended

Title is the title of the event to be attended

### Possible responces:

* Unauthorised
* Query Error
* Success
* Already Attending

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong

A reponse of Query Error means some kind of error occured in MySQL when attending the event this could be a lot of different things e.g. the event doesn't exist or some generic server error occured

A response of Success means the event was successfully given an attending status for the current user

A response of Already Attending means the user is already attending the event that you tried to add an attending status to



## Load all events attending for current user:

### To load all events attending you must send a POST request to the server containing the following values:

* formname
* username
* password

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **eventsAttendingReady**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

### Possible responces:

* Unauthorised
* JSON array

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong

A JSON array will contain all the events being attended by the current user


## Load all events attending:

### To load all events attending you must send a POST request to the server containing the following values:

* formname
* username
* password

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **loadAllAttending**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

### Possible responces:

* Unauthorised
* JSON array

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong

A JSON array will contain all the events being attended by every user


## Unattend an event (Subject to change):

### To load all events attending you must send a POST request to the server containing the following values:

* formname
* username
* password
* ID

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **unattendEvent**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

ID is the ID of the event to be unattended

### Possible responces:

* Unauthorised
* Query Error
* Success

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong

A reponse of Query Error means some kind of error occured in MySQL when unattending the event this could be a lot of different things e.g. some generic server error occured

A response of Success means that the events attending status was successfully removed


## Changing of username:

### To change username you must send a POST request to the server containing the following values:

* formname
* username
* password
* updateUsername

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **changeUsername**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

ID is the ID of the event to be unattended

### Possible responces:

* Unauthorised
* Query Error
* Success

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong

A reponse of Query Error means some kind of error occured in MySQL when changing the username this could be a lot of different things e.g. some generic server error occured

A response of Success means that the username was changed successfully


## Changing of password:

### To change username you must send a POST request to the server containing the following values:

* formname
* username
* password
* updatePassword

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **changePassword**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

updatePassword is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

### Possible responces:

* Unauthorised
* Query Error
* Success

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong

A reponse of Query Error means some kind of error occured in MySQL when changing the password this could be a lot of different things e.g. some generic server error occured

A response of Success means that the password was changed successfully


## Changing of email:

### To change username you must send a POST request to the server containing the following values:

* formname
* username
* password
* updatePassword

#### Explanation:

Formname defines what part of the API you want to use so in this case the formname would be **changeEmail**

Username is just the account username

Password is the user password encrypted using aes-256-cbc with the following keys:

* ENC_KEY = "eb45707674371ce8259b2153c7b6a453"
* IV = "70cd8558247bed84"

updateEmail is the new email for the user

### Possible responces:

* Unauthorised
* Query Error
* Success

#### Explanation:

A response of Unauthorised means users identity could not be confirmed the username/password were wrong

A reponse of Query Error means some kind of error occured in MySQL when changing the email this could be a lot of different things e.g. some generic server error occured

A response of Success means that the email was changed successfully
