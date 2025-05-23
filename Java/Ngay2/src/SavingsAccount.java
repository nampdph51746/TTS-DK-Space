public class SavingsAccount extends BankAccount {
    private double interestRate;

    public SavingsAccount(String accountNumber, Person owner, double initialBalance, double interestRate) {
        super(accountNumber, owner, initialBalance, AccountType.SAVINGS);
        this.interestRate = interestRate;
    }

    @Override
    public boolean withdraw(double amount) {
        if (amount <= 0) {
            System.out.println("Error: Withdrawal amount must be positive.");
            return false;
        }
        if (amount > getBalance()) {
            System.out.println("Error: Insufficient balance.");
            return false;
        }
        balance -= amount;
        getTransactions().add(new Transaction("TX" + System.currentTimeMillis(), TransactionType.WITHDRAW, amount));
        System.out.println("Withdrawn " + amount + ". New balance: " + balance);
        return true;
    }

    public double getInterestRate() {
        return interestRate;
    }

    @Override
    public void printSummary() {
        System.out.println("Savings Account: " + getAccountNumber() + ", Balance: " + getBalance() + ", Interest Rate: " + interestRate);
    }
}