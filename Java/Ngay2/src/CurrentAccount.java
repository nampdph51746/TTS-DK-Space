public class CurrentAccount extends BankAccount {
    private double overdraftLimit;

    public CurrentAccount(String accountNumber, Person owner, double initialBalance, double overdraftLimit) {
        super(accountNumber, owner, initialBalance, AccountType.CURRENT);
        this.overdraftLimit = overdraftLimit;
    }

    @Override
    public boolean withdraw(double amount) {
        if (amount <= 0) {
            System.out.println("Error: Withdrawal amount must be positive.");
            return false;
        }
        if (amount > getBalance() + overdraftLimit) {
            System.out.println("Error: Exceeds overdraft limit.");
            return false;
        }
        balance -= amount;
        getTransactions().add(new Transaction("TX" + System.currentTimeMillis(), TransactionType.WITHDRAW, amount));
        System.out.println("Withdrawn " + amount + ". New balance: " + balance);
        return true;
    }

    public double getOverdraftLimit() {
        return overdraftLimit;
    }

    @Override
    public void printSummary() {
        System.out.println("Current Account: " + getAccountNumber() + ", Balance: " + getBalance() + ", Overdraft Limit: " + overdraftLimit);
    }
}