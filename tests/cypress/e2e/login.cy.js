describe('Login Form', () => {
    
    beforeEach(() => {
        cy.wait(3000);
        cy.visit('https://app.invoicing.co/#/login');
         cy.wait(3000);
    });

    

  let testCounter = 1; // initialize a counter

afterEach(function () {
  const screenshotName = `T${testCounter}`; // T1, T2, T3...
  cy.screenshot(screenshotName, { capture: 'fullPage', overwrite: true });
  testCounter++; // increment for next test
});
   


    // ------------------------------------------------
    // 1. Email Validation Tests
    // ------------------------------------------------
    it('T-1 Shows error when email is empty', () => {
        cy.get('input[name="password"]').type('SomePassword123');
        cy.get('button[type="submit"]').click();

        cy.contains('The email field is required.').should('be.visible');
    });

    it('T-2 Shows browser validation error when email is missing @', () => {
    cy.get('input[name="email"]').type('xyz');
    cy.get('button[type="submit"]').click();

    cy.get('input[name="email"]').then(($input) => {
        expect($input[0].validationMessage)
            .to.contain("Please include an '@' in the email address");
    });
});

it('T-3 Shows error when email has @ but incomplete domain', () => {
    cy.get('input[name="email"]').type('xyz@');
    cy.get('button[type="submit"]').click();

    cy.get('input[name="email"]').then(($input) => {
        expect($input[0].validationMessage)
            .to.contain("Please enter a part following '@'");
    });
});


    it('T-4 Shows "email not found" when email is invalid but correctly formatted', () => {
        cy.get('input[name="email"]').type('i233023@isb.nu.edu.pkk');
        cy.get('input[name="password"]').type('SomePassword123');
        cy.get('button[type="submit"]').click();

        cy.contains('Email not set or not found').should('be.visible');
    });

    // ------------------------------------------------
    // 2. Password Validation Tests
    // ------------------------------------------------
    it('T-5 Shows error when password is empty', () => {
        cy.get('input[name="email"]').type('i233023@isb.nu.edu.pk');
        cy.get('button[type="submit"]').click();

        cy.contains('The password field is required.').should('be.visible');
    });

   it('T-6 Wrong password reloads back to login page', () => {
    cy.get('input[name="email"]').type('i233023@isb.nu.edu.pk');
    cy.get('input[name="password"]').type('xyz');
    cy.get('button[type="submit"]').click();
    cy.url({ timeout: 5000 }).should('include', '/login');
    cy.get('button[type="submit"]').should('be.visible');
});

    it('T-7 Both fields empty → only email error shown first', () => {
        cy.get('button[type="submit"]').click();

        cy.contains('The email field is required.').should('be.visible');
    });

    it('T-8 Email empty + password filled → email error', () => {
        cy.get('input[name="password"]').type('SomePassword123');
        cy.get('button[type="submit"]').click();

        cy.contains('The email field is required.').should('be.visible');
    });

    it('T-8 Email filled + password empty → password error', () => {
        cy.get('input[name="email"]').type('i233023@isb.nu.edu.pk');
        cy.get('button[type="submit"]').click();

        cy.contains('The password field is required.').should('be.visible');
    });

    // ------------------------------------------------
    // 3. 2FA Field Tests
    // ------------------------------------------------
    it('T-9 2FA field is optional → leaving empty should not show error', () => {
        cy.get('input[name="email"]').type('i233023@isb.nu.edu.pk');
        cy.get('input[name="password"]').type('SomePassword123');
        cy.get('button[type="submit"]').click();

        // No 2FA error expected
        cy.contains('one-time password').should('not.exist');
    });

   it('T-10 Shows "no credentials match" when wrong 2FA code entered', () => {
    cy.get('input[name="email"]').type('i233023@isb.nu.edu.pk');
    cy.get('input[name="password"]').type('SomePassword123');
    cy.get('input[name="one_time_password"]').type('XYZ');
    cy.get('button[type="submit"]').click();
    cy.url({ timeout: 5000 }).should('include', '/login');
    cy.get('button[type="submit"]').should('be.visible');
});

});