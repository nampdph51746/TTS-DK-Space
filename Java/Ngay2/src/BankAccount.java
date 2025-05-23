import java.time.LocalDate;
import java.time.LocalDateTime;
import java.util.ArrayList;
import java.util.List;

public abstract class BankAccount implements Printable {
    private String accountNumber;
    private Person owner;
    protected double balance;
    private LocalDate createdDate;
    private List<Transaction> transactions;
    private AccountType accountType;

    public static class Transaction {
        private String id;
        private TransactionType type;
        private double amount;
        private LocalDateTime timestamp;

        public Transaction(String id, TransactionType type, double amount) {
            this.id = id;
            this.type = type;
            this.amount = amount;
            this.timestamp = LocalDateTime.now();
        }

        @Override
        public String toString() {
            return "Transaction ID: " + id + ", Type: " + type + ", Amount: " + amount + ", Time: " + timestamp;
        }
    }

    // Constructor
    public BankAccount(String accountNumber, Person owner, double initialBalance, AccountType accountType) {
        this.accountNumber = accountNumber;
        this.owner = owner;
        this.balance = initialBalance;
        this.createdDate = LocalDate.now();
        this.transactions = new ArrayList<>();
        this.accountType = accountType;

        if (initialBalance > 0) {
            transactions.add(new Transaction("TX" + System.currentTimeMillis(), TransactionType.DEPOSIT, initialBalance));
        }
    }

    // Getters
    public String getAccountNumber() {
        return accountNumber;
    }

    public Person getOwner() {
        return owner;
    }

    public double getBalance() {
        return balance;
    }

    public LocalDate getCreatedDate() {
        return createdDate;
    }

    public AccountType getAccountType() {
        return accountType;
    }

    public List<Transaction> getTransactions() {
        return transactions;
    }

    public void deposit(double amount) {
        if (amount <= 0) {
            System.out.println("Error: Deposit amount must be positive.");
            return;
        }
        balance += amount;
        transactions.add(new Transaction("TX" + System.currentTimeMillis(), TransactionType.DEPOSIT, amount));
        System.out.println("Deposited " + amount + ". New balance: " + balance);
    }

    public abstract boolean withdraw(double amount);

    public void printAccountInfo() {
        System.out.println("Account Number: " + accountNumber);
        System.out.println("Owner: " + owner.getFullName());
        System.out.println("Type: " + accountType);
        System.out.println("Balance: " + balance);
        System.out.println("Created Date: " + createdDate);
        System.out.println("Transaction History:");
        if (transactions.isEmpty()) {
            System.out.println("  No transactions.");
        } else {
            for (Transaction tx : transactions) {
                System.out.println("  " + tx);
            }
        }
    }

    @Override
    public void printSummary() {
        System.out.println("Summary: Account " + accountNumber + " (" + accountType + "), Balance: " + balance);
    }
}