describe('Settings Form', () => {

  function doLogin() {
  cy.visit('https://app.invoicing.co/#/login');
  cy.get('input[type="email"]').type('i233053@isb.nu.edu.pk');
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
    cy.get('button[type="submit"]').contains('Save').click();
  }

  function selectReactOption(index, option) {
    cy.get('.css-b62m3t-container')
      .eq(index)
      .click({ force: true });

    cy.get('input[role="combobox"][aria-autocomplete="list"]')
      .eq(index)
      .type(option + '{enter}', { force: true });
  }

  beforeEach(() => {
    cy.visit('https://app.invoicing.co/#/login');
    doLogin();
    cy.url({ timeout: 20000 }).should('include', '/dashboard');
    cy.visit('https://app.invoicing.co/#/clients/create/settings');
    cy.get('body', { timeout: 15000 }).should('exist'); 
    cy.wait(2000); 
  });


  
  let testCounter = 1; // initialize a counter

afterEach(function () {
  const screenshotName = `T${testCounter}`; // T1, T2, T3...
  cy.screenshot(screenshotName, { capture: 'fullPage', overwrite: true });
  testCounter++; // increment for next test
});

  it('S-1 Fills all fields and submits successfully', () => {
    selectReactOption(0, 'Algerian');    // Currency
    selectReactOption(1, 'Albanian');    // Language
    selectReactOption(2, 'Net 14');      // Payment Terms
    selectReactOption(3, 'Net 14');      // Quote Valid Until
      selectReactOption(4, 'Enabled');     // Send Reminders
    selectReactOption(5, '4-10');        // Company Size
    selectReactOption(6, 'Advertising'); // Industry

    submitForm();
    cy.contains(/clients/i, { timeout: 5000 }).should('exist');
  });


  it('S-4 Reminders disable', () => {
    selectReactOption(4, 'Disabled');    // Send Reminders
    submitForm();
    cy.contains(/clients/i, { timeout: 5000 }).should('exist');
  });

 
  it('S-5 Reminders enable', () => {
    selectReactOption(4, 'Enabled');    // Send Reminders
    submitForm();
    cy.contains(/clients/i, { timeout: 5000 }).should('exist');
  });

  it('S-6 Task Rate Empty', () => {
    selectReactOption(0, 'Algerian');    // Currency
    selectReactOption(1, 'Albanian');    // Language
    selectReactOption(2, 'Net 14');      // Payment Terms
    selectReactOption(3, 'Net 14');      // Quote Valid Until
    selectReactOption(4, 'Enabled');     // Send Reminders
    selectReactOption(5, '4-10');        // Company Size
    selectReactOption(6, 'Advertising'); // Industry
    submitForm();
    cy.contains(/clients/i, { timeout: 5000 }).should('exist');
  });


  it('S-7 Task rate Negative', () => {
    cy.get('input[type="text"][inputmode="numeric"]').type('-1'); 
    submitForm();
    cy.contains(/clients/i, { timeout: 5000 }).should('exist');
  });



  it('S-8 Task upper boundary', () => {
    cy.get('input[type="text"][inputmode="numeric"]').type('101'); 
    submitForm();
    cy.contains(/clients/i, { timeout: 5000 }).should('exist');
  });

  
});
