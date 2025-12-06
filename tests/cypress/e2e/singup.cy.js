describe('Sign Up Form', () => {

    beforeEach(() => {
        cy.visit('https://app.invoicing.co/#/register');
});



  let testCounter = 1; // initialize a counter

afterEach(function () {
  const screenshotName = `T${testCounter}`; // T1, T2, T3...
  cy.screenshot(screenshotName, { capture: 'fullPage', overwrite: true });
  testCounter++; // increment for next test
});
    function submitForm() {
      cy.get('button[type="submit"]', { timeout: 15000 })
      .should('be.visible')
      .click();
      cy.get('button[type="submit"]', { timeout: 15000 })
      .should('be.visible')
      .click();


}

    // Helper function to check if "Register" is anywhere on the page
    function cyContainRegister() {
        cy.contains('Register', { matchCase: false }).should('exist');
    }

    // ------------------------------------------------
    // 1. Email Validation Tests
    // ------------------------------------------------
    it('R-1 Shows error when email is empty', () => {
        cy.get('#password', { timeout: 15000 }).should('be.visible').type('SomePassword123');
        cy.get('#password_confirmation', { timeout: 15000 }).should('be.visible').type('SomePassword123');
        cyContainRegister();
    });

    it('R-2 Shows browser validation error when email is missing @', () => {
        cy.get('#email').type('xyz');
        cy.get('#password').type('SomePassword123');
        cy.get('#password_confirmation').type('SomePassword123');
        cyContainRegister();
    });

    it('R-3 Shows error when email has @ but incomplete domain', () => {
        cy.get('#email').type('xyz@');
        cy.get('#password').type('SomePassword123');
        cy.get('#password_confirmation').type('SomePassword123');
        cyContainRegister();
    });

    it('R-4 Shows error for invalid email but correctly formatted', () => {
        cy.get('#email').type('i233023@isb.nu.edu.pkk');
        cy.get('#password').type('SomePassword123');
        cy.get('#password_confirmation').type('SomePassword123');
       
        cyContainRegister();
    });

    // ------------------------------------------------
    // 2. Password Validation Tests
    // ------------------------------------------------
    it('R-5 Shows error when password is empty', () => {
        cy.get('#email').type('i233023@isb.nu.edu.pk');
        cy.get('#password_confirmation').type('SomePassword123');
      
        cyContainRegister();
    });

    it('R-6 Shows error when password confirmation does not match', () => {
        cy.get('#email').type('i233023@isb.nu.edu.pk');
        cy.get('#password').type('nisha2005');
        cy.get('#password_confirmation').type('nisha2006');
        
        cyContainRegister();
    });

    it('R-7 Shows error when password confirmation is empty', () => {
        cy.get('#email').type('i233023@isb.nu.edu.pk');
        cy.get('#password').type('nisha2005');
      
        cyContainRegister();
    });

    // ------------------------------------------------
    // 3. Both fields empty
    // ------------------------------------------------
    it('R-8 Both fields empty → show multiple errors', () => {
        
        cyContainRegister();
    });

    // ------------------------------------------------
    // 4. Email & Password Length Boundary Tests
    // ------------------------------------------------
    it('R-9: Email below min length', () => {
        cy.get('#email').type('a@b.c');
        cy.get('#password').type('SomePassword123');
        cy.get('#password_confirmation').type('SomePassword123');
      
        cyContainRegister();
    });

    it('R-10: Email above max length', () => {
        cy.get('#email').type('verylongemailaddress@exampledomain.com');
        cy.get('#password').type('SomePassword123');
        cy.get('#password_confirmation').type('SomePassword123');
     
        cyContainRegister();
    });

    it('R-11: Password below min length', () => {
        cy.get('#email').type('i233023@isb.nu.edu.pk');
        cy.get('#password').type('abc123');
        cy.get('#password_confirmation').type('abc123');
      
        cyContainRegister();
    });

    it('R-12: Password above max length', () => {
        cy.get('#email').type('i233023@isb.nu.edu.pk');
        cy.get('#password').type('APasswordThatIsWayTooLongBeyond50Chars12345');
        cy.get('#password_confirmation').type('APasswordThatIsWayTooLongBeyond50Chars12345');
      
        cyContainRegister();
    });

});