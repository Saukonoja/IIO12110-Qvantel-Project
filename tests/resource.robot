*** Settings ***

Library           Selenium2Library
Library		  FakerLibrary
*** Variables ***

${HOST}           	54.89.144.228
${INDEX_URL}      	http://${HOST}/
${WELCOME URL}    	http://${HOST}/
${REGISTER_URL}		http://${HOST}/registration
${LOGIN_URL}		http://${HOST}/login
${ADMINLOGIN_URL}	http://${HOST}/user/login
${PHONES_URL}		http://${HOST}/phones
${PANTS_URL}		http:${HOST}/pants
${JACKETS_URL}		http://${HOST}/jackets
${MANAGE_URL}		http://${HOST}/manage

${BROWSER}        	Firefox

${USERNAME}	  	asd
${PASSWORD}	  	asd
${FALSE_USERNAME}	hottamale
${FALSE_PASSWORD}	hottamale
${ADMIN_USER}		qmies
${ADMIN_PASS}		hyvadrupalpassu


${USERNAME_LOCATOR}	  name
${PASSWORD_LOCATOR}	  pass
${SUBMIT_LOCATOR}	  op
${SHOPPINGCART_LOCATOR}	  shoppingCartContainer
${COUNT_LOCATOR}	  count

*** Keywords ***

Generate User
    ${TEST_USERNAME}=    User Name
    ${TEST_PASSWORD}=    Password
    Log To Console    ${TEST_USERNAME}
    Log To Console    ${TEST_PASSWORD}

Generate Password

    [Tags]    Faker
    Comment    Generate Address
    ${TEST_PASSWORD}    Country
    Log To Console    ${TEST_PASSWORD}

Register New User
    ${TEST_USERNAME}=    User Name
    ${TEST_PASSWORD}=    Password
    Go To Register Page
    Wait And Check If Page Contains    Registration
    Input Name And Password    ${TEST_USERNAME}    ${TEST_PASSWORD}
    Send Form
    Close Browser

Logged In User Should have Shopping Cart
    [Arguments]    ${arg1}    ${arg2}    ${arg3}

    Wait Until Page Contains Element    ${SHOPPINGCART_LOCATOR}
    Page Should Contain Element    ${SHOPPINGCART_LOCATOR}
    Wait Until Page Contains Element    ${COUNT_LOCATOR}
    Page Should Contain Element    ${COUNT_LOCATOR}
    Wait Until Page Contains    Jackets
    Click Link    ${arg1}

    Wait Until Page Contains Element    ${SHOPPINGCART_LOCATOR}
    Page Should Contain Element    ${SHOPPINGCART_LOCATOR}
    Wait Until Page Contains Element    ${COUNT_LOCATOR}
    Page Should Contain Element    ${COUNT_LOCATOR}
    Wait Until Page Contains    Pants
    Click Link    ${arg2}

    Wait Until Page Contains Element    ${SHOPPINGCART_LOCATOR}
    Page Should Contain Element    ${SHOPPINGCART_LOCATOR}
    Wait Until Page Contains Element    ${COUNT_LOCATOR}
    Page Should Contain Element    ${COUNT_LOCATOR}
    Wait Until Page Contains    Phones
    Click Link    ${arg3}

    Wait Until Page Contains Element    ${SHOPPINGCART_LOCATOR}
    Page Should Contain Element    ${SHOPPINGCART_LOCATOR}
    Wait Until Page Contains Element    ${COUNT_LOCATOR}
    Page Should Contain Element    ${COUNT_LOCATOR}



Open Index Page
    Open Browser    ${INDEX_URL}    ${BROWSER}

Go To Admin Login Page
    Go To    ${ADMINLOGIN_URL}

Go To Register Page
    Go To    ${REGISTER_URL}

Go To Login Page
    Go To    ${LOGIN_URL}

Go To Phones Page
    Go To    ${PHONES_URL}

Go To Admin Page
    Go To    ${MANAGE_URL}

Go To Jackets Page
    Go To    ${JACKETS_URL}

Wait And Check If Page Contains
    [Arguments]    ${arg1}
    Wait Until Page Contains    ${arg1}
    Page Should Contain    ${arg1}

Input Name
    [Arguments]    ${arg1}
    Wait Until Page Contains Element    ${USERNAME_LOCATOR}
    Input Text    ${USERNAME_LOCATOR}   ${arg1}

Input Password
    [Arguments]    ${arg1}
    Wait Until Page Contains Element    ${PASSWORD_LOCATOR}
    Input Text    ${PASSWORD_LOCATOR}    ${arg1}

Input Name And Password
    [Arguments]    ${arg1}    ${arg2}
    Wait Until Page Contains Element    ${USERNAME_LOCATOR}
    Input Text    ${USERNAME_LOCATOR}    ${arg1}
    Wait Until Page Contains Element    ${PASSWORD_LOCATOR}
    Input Text    ${PASSWORD_LOCATOR}    ${arg2}

Input Testdata Name And Password
    Wait Until Page Contains Element    ${USERNAME_LOCATOR}
    Input Text    ${USERNAME_LOCATOR}    ${TEST_USERNAME}
    Wait Until Page Contains Element    ${PASSWORD_LOCATOR}
    Input Text    ${PASSWORD_LOCATOR}    ${TEST_PASSWORD}

Send Form
    Wait Until Page Contains Element    ${SUBMIT_LOCATOR}
    Click Button    ${SUBMIT_LOCATOR}

High Priority Customer
    [Arguments]    ${arg1}    ${arg2}    ${arg3}
    Submit Form    ${arg1}
    Page Should Not Contain    ${arg2}
    Click Element    ${arg1}
    Page Should Not Contain    ${arg2}
    Click Link    ${arg1}
    Page SHould Not Contain    ${arg2}
    Click Image    ${arg1}
    Page Should Not Contain    ${arg2}
    Submit Form    ${arg1}
    Page Should Contain    ${arg2}
    Page Should Contain Image    ${arg3}
Delay
    [Arguments]    ${arg1}
    Set Selenium Speed    ${arg1} seconds

Product Test
    [Arguments]    ${arg1}    ${arg2}    ${arg3}    ${arg4}
    Wait Until Page Contains Element    ${arg1}
    Page Should Contain Image    ${arg1}
    Page Should Contain    ${arg2}
    Page Should Contain    ${arg3}
    Page Should Contain    ${arg4}

Commercial Test
    [Arguments]    ${arg1}    ${arg2}    ${arg3}    ${arg4}
    Wait Until Page Contains Element    //*[@id="${arg1}"]
    Click Button    //*[@id="${arg1}"]
    Wait Until Page Contains Element    //*[@id="${arg2}"]
    Click Button    //*[@id="${arg2}"]
    Capture Page Screenshot
    Wait Until Page Contains Element    //*[@id="${arg1}"]
    Click Button    //*[@id="${arg1}"]
    Capture Page Screenshot
    Wait Until Page Contains Element    ${arg3}
    Page Should Contain Image    ${arg4}
    Capture Page Screenshot

Admin Add Content To Jackets
    Open Index Page
    Go To Admin Login Page
    Wait And Check If Page Contains    User account
    Input Name And Password    qmies    hyvadrupalpassu
    Send Form
    Page Should Contain    qmies
    Go To Admin Page


Admin Sign In
    Go To Admin Login Page
    Wait And Check If Page Contains    User account
    Input Name And Password    ${ADMIN_USER}    ${ADMIN_PASS}
    Send Form
    Wait Until Page Contains    qmies
    Page Should Contain    qmies

Manage Page Check
    Go To Admin Page
    Wait Until Page Contains Element    page-title
    Element Should Contain    page-title    Admin UI

