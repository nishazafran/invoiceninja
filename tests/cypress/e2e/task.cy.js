describe('Task Form Tests', () => {
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
    cy.wait(1000);
    cy.visit('https://app.invoicing.co/#/tasks/create');
    cy.wait(5000);
  });



  
  let testCounter = 1; // initialize a counter

afterEach(function () {
  const screenshotName = `T${testCounter}`; // T1, T2, T3...
  cy.screenshot(screenshotName, { capture: 'fullPage', overwrite: true });
  testCounter++; // increment for next test
});
  it('T-1: All fields empty', () => {
    cy.get('button[type="submit"]').click();
    cy.contains('Task').should('exist');
  });

  it('T-2: All fields correctly filled', () => {
    cy.get('input[data-testid="combobox-input-field"]').eq(0).click().type('nisha{enter}', { force: true }); // Client
    cy.get('input[data-testid="combobox-input-field"]').eq(1).click().type('p1{enter}', { force: true });    // Project
    cy.get('input[autocomplete="new-password"]').type('50', { force: true }); // Task Number
    cy.get('input[inputmode="numeric"]').type('50', { force: true });         // Rate
    cy.get('input[data-testid="combobox-input-field"]').eq(2).click().type('backlog{enter}', { force: true }); // Status
    cy.get('textarea').type('Task description here', { force: true });        // Description
    cy.get('button[type="submit"]').click();
    cy.contains('Task').should('exist');
  });

  it('T-3: Invalid client', () => {
   cy.get('input[data-testid="combobox-input-field"]').eq(0).click().type('john', { force: true }); // Client
    cy.get('button[type="submit"]').click();
    cy.contains('Task').should('exist'); 
  });

  it('T-4: Invalid project', () => {
     cy.get('input[data-testid="combobox-input-field"]').eq(1).click().type('abc', { force: true });    // Project
    cy.get('button[type="submit"]').click();
    cy.contains('Task').should('exist'); 
  });

   it('T-5: Task number BVA lower', () => {
    cy.get('input[autocomplete="new-password"]').eq(0).type('-1', { force: true });
    cy.get('button[type="submit"]').click();
    cy.contains('Task').should('exist');
});



   it('T-5b: Task number BVA upper', () => {
    cy.get('input[autocomplete="new-password"]').eq(0).type('101', { force: true });
    cy.get('button[type="submit"]').click();
    cy.contains('Task').should('exist');
});


  it('T-6: Rate BVA', () => {
    cy.get('input[inputmode="numeric"]').type('-1', { force: true });
    cy.get('button[type="submit"]').click();
    cy.contains('Task').should('exist');
    cy.get('input[inputmode="numeric"]').clear().type('101', { force: true });
    cy.get('button[type="submit"]').click();
    cy.contains('Task').should('exist');
  });

  it('T-7: Invalid status', () => {
    cy.get('input[data-testid="combobox-input-field"]').eq(2).type('active{enter}', { force: true });
    cy.get('button[type="submit"]').click();
    cy.contains('Task').should('exist');
  });

  });
