describe('Payment Form Tests', () => {

   function doLogin() {
  cy.visit('https://app.invoicing.co/#/login');
  cy.get('input[type="email"]').type('nisha@gmail.com');
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

  beforeEach(() => {
    doLogin();
    cy.wait(1000);
    cy.visit('https://app.invoicing.co/#/payments/create');
     cy.wait(3000);
  });


  
  let testCounter = 1; // initialize a counter

afterEach(function () {
  const screenshotName = `T${testCounter}`; // T1, T2, T3...
  cy.screenshot(screenshotName, { capture: 'fullPage', overwrite: true });
  testCounter++; // increment for next test
});
  it('T-1: All fields empty', () => {
    cy.get('button[type="submit"]').click();
    cy.contains('The client id field is required.').should('exist');
  });

  it('T-2: All fields correctly filled', () => {
    cy.get('input[data-testid="combobox-input-field"]').first().type('nisha{enter}');
    cy.get('input[inputmode="numeric"]').type('50', { force: true });
    cy.get('#date').type('2025-12-01');
    cy.get('.css-1y9i3r8 input[role="combobox"]')
  .should('exist')
  .type('ACH{enter}', { force: true });
      cy.get('button[type="submit"]').click();
    cy.contains('edit').should('exist');
  });

  it('T-3: Invalid client', () => {
    cy.get('input[data-testid="combobox-input-field"]').first().type('John');
    cy.get('button[type="submit"]').click();
    cy.contains('The client id field is required.').should('exist');
  });

  it('T-4: Negative amount', () => {
    cy.get('input[data-testid="combobox-input-field"]').first().type('nisha{enter}');
   cy.get('input[inputmode="numeric"]').type('-1', { force: true });
    cy.get('button[type="submit"]').click();
    cy.contains('This cannot be used as a credit or payment.').should('exist');
  });

  it('T-5: Amount exceeds max allowed', () => {
    cy.get('input[data-testid="combobox-input-field"]').first().type('nisha{enter}');
   cy.get('input[inputmode="numeric"]').type('10000000000000', { force: true });
    cy.get('button[type="submit"]').click();
    cy.contains('The client id field is required.').should('exist');
  });

  it('T-6: Invalid payment type', () => {
     cy.get('input[data-testid="combobox-input-field"]').first().type('John');
    cy.get('.css-1y9i3r8 input[role="combobox"]')
  .should('exist')
  .type('XYZ', { force: true });
    cy.get('button[type="submit"]').click();
    cy.contains('The client id field is required.').should('exist');
  });

});
