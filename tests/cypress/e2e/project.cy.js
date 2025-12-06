describe('Project Create Form Tests', () => {

   function doLogin() {
  cy.visit('https://app.invoicing.co/#/login');
  cy.get('input[type="email"]').type('nishaa@gmail.com');
  cy.get('input[type="password"]').type('nisha2006');
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
    cy.visit('https://app.invoicing.co/#/projects/create');
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
    cy.get('input[data-cy="name"]').type('Project 1');
   cy.get('input[data-testid="combobox-input-field"]')
  .first()
  .click({ force: true })           
  .clear({ force: true })           
  .type('nisha{enter}', { force: true });  
    cy.get('input[type="date"]').type('2025-12-01');
    cy.get('textarea').first().type('Public note');
    cy.get('textarea').eq(1).type('Private note');
    cy.get('button[type="submit"]').click();
    cy.contains('edit').should('exist');
  });

  it('T-3: Invalid Project Name', () => {
    cy.get('button[type="submit"]').click();
    cy.contains('The name field is required.').should('exist');
  });

  it('T-4: Invalid Client', () => {
   cy.get('input[data-testid="combobox-input-field"]')
  .first()
  .click({ force: true })           
  .clear({ force: true })           
  .type('john', { force: true });  
    cy.get('button[type="submit"]').click();
    cy.contains('The client id field is required.').should('exist');
  });

  it('T-6: Invalid Budget Hours', () => {
    cy.get('input[type="text"]').eq(1).type('-1');
    cy.get('button[type="submit"]').click();
    cy.contains('The name field is required.').should('exist');

    cy.get('input[type="text"]').eq(1).clear().type('101');
    cy.get('button[type="submit"]').click();
    cy.contains('The name field is required.').should('exist');
  });

  it('T-7: Invalid Task Rate', () => {
    cy.get('input[type="text"]').eq(2).type('-1');
    cy.get('button[type="submit"]').click();
    cy.contains('The client id field is required.').should('exist');

    cy.get('input[type="text"]').eq(2).clear().type('101');
    cy.get('button[type="submit"]').click();
    cy.contains('The client id field is required.').should('exist');
  });

});
