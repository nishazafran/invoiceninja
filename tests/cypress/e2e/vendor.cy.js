describe('Vendor Form', () => {

  beforeEach(() => {
    doLogin();
    cy.visit('https://app.invoicing.co/#/vendors/create');
    cy.wait(2000);
});
 function doLogin() {
  cy.visit('https://app.invoicing.co/#/login');
  cy.get('input[type="email"]').type('mahamsatti2003@gmail.com');
  cy.get('input[type="password"]').type('nisha2005');
  cy.get('button[type="submit"]').click();
  cy.url().should('include', '/dashboard');
  cy.wait(1000);
  cy.get('svg[viewBox="0 0 12 12"]', { timeout: 2000 })
    .then($svg => {
      if ($svg.length) {
        cy.wrap($svg.first()).click({ force: true });
      }
    });
}


function submitForm() {
    cy.get('form', { timeout: 20000 }).should('exist');
    cy.contains('button', 'Save', { timeout: 20000 })
      .should('be.visible')
      .scrollIntoView()
      .click();
    cy.wait(700);
}


  let testCounter = 1; // initialize a counter

afterEach(function () {
  const screenshotName = `T${testCounter}`; // T1, T2, T3...
  cy.screenshot(screenshotName, { capture: 'fullPage', overwrite: true });
  testCounter++; // increment for next test
});


    // ------------------------------------------------
    // 1. Empty fields validation
    // ------------------------------------------------
    it('C-1 Shows error when all fields empty', () => {
        submitForm();
        cy.contains('The name field is required').should('be.visible'); // invalid_name translation
    });

    // ------------------------------------------------
    // 2. Invalid name/number validations
    // ------------------------------------------------
    it('C-2 Shows error when company name is number', () => {
        cy.get('input[autocomplete="new-password"]').first().type('01');
        submitForm();
        cy.contains('The name field is required').should('be.visible');
    });

    it('C-3 Shows error when ID number is 01', () => {
      cy.get('#name').type('Nisha');
      cy.get('input[autocomplete="new-password"]').eq(2).type('01'); // ID Number
        submitForm();
        cy.contains('Details').should('be.visible');
    });

    // ------------------------------------------------
    // 3. Invalid email
    // ------------------------------------------------
    it('C-4 Shows error when contact email is invalid', () => {
        cy.get('#first_name_0').type('Nisha');
        cy.get('input[id="last_name_0"]').type('Zafran'); // assumed ID
        cy.get('#email_0').type('nisha'); // invalid email
        submitForm();
        cy.contains('The contacts.0.email must be a valid email address.').should('be.visible');
    });

    // ------------------------------------------------
    // 4. Valid email and names (example)
    // ------------------------------------------------
    it('C-5 Fills valid client and contact info', () => {
        cy.get('input[autocomplete="new-password"]').first().type('My Company'); // company name
        cy.get('#first_name_0').type('Nisha');
        cy.get('input[id="last_name_0"]').type('Zafran');
        cy.get('#email_0').type('nisha@isb.nu.edu.pk');
        submitForm();
        cy.contains('Details').should('be.visible'); // adjust success message
    });

});
