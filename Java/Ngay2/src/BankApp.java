import java.util.ArrayList;
import java.util.List;
import java.util.Scanner;

public class BankApp {
    private List<BankAccount> accounts = new ArrayList<>();
    private Scanner scanner = new Scanner(System.in);

    public void createAccount() {
        System.out.print("Enter Person ID: ");
        String id = scanner.nextLine();
        System.out.print("Enter Full Name: ");
        String fullName = scanner.nextLine();
        System.out.print("Enter Email: ");
        String email = scanner.nextLine();
        System.out.print("Enter Phone Number: ");
        String phoneNumber = scanner.nextLine();
        Person owner = new Person(id, fullName, email, phoneNumber);

        System.out.print("Enter Account Number: ");
        String accountNumber = scanner.nextLine();
        System.out.print("Enter Initial Balance: ");
        double initialBalance = scanner.nextDouble();
        scanner.nextLine();
        System.out.print("Enter Account Type (1 for Savings, 2 for Current): ");
        int type = scanner.nextInt();
        scanner.nextLine();

        BankAccount account;
        if (type == 1) {
            System.out.print("Enter Interest Rate: ");
            double interestRate = scanner.nextDouble();
            scanner.nextLine();
            account = new SavingsAccount(accountNumber, owner, initialBalance, interestRate);
        } else {
            System.out.print("Enter Overdraft Limit: ");
            double overdraftLimit = scanner.nextDouble();
            scanner.nextLine();
            account = new CurrentAccount(accountNumber, owner, initialBalance, overdraftLimit);
        }
        accounts.add(account);
        System.out.println("Account created successfully.");
    }

    public void performTransaction() {
        System.out.print("Enter Account Number: ");
        String accountNumber = scanner.nextLine();
        BankAccount account = findAccount(accountNumber);
        if (account == null) {
            System.out.println("Account not found.");
            return;
        }

        System.out.print("Enter Transaction Type (1 for Deposit, 2 for Withdraw): ");
        int type = scanner.nextInt();
        System.out.print("Enter Amount: ");
        double amount = scanner.nextDouble();
        scanner.nextLine();

        if (type == 1) {
            account.deposit(amount);
        } else {
            account.withdraw(amount);
        }
    }

    public void displayAccountInfo() {
        System.out.print("Enter Account Number: ");
        String accountNumber = scanner.nextLine();
        BankAccount account = findAccount(accountNumber);
        if (account == null) {
            System.out.println("Account not found.");
        } else {
            account.printAccountInfo();
        }
    }

    public void displayAllSummaries() {
        if (accounts.isEmpty()) {
            System.out.println("No accounts available.");
            return;
        }
        System.out.println("All Account Summaries:");
        for (BankAccount account : accounts) {
            account.printSummary();
        }
    }

    private BankAccount findAccount(String accountNumber) {
        for (BankAccount account : accounts) {
            if (account.getAccountNumber().equals(accountNumber)) {
                return account;
            }
        }
        return null;
    }

    public void showMenu() {
        while (true) {
            System.out.println("\n=== Bank Management System ===");
            System.out.println("1. Create new account");
            System.out.println("2. Perform transaction");
            System.out.println("3. Display account info");
            System.out.println("4. Display all account summaries");
            System.out.println("5. Exit");
            System.out.print("Enter your choice: ");

            int choice;
            try {
                choice = Integer.parseInt(scanner.nextLine());
            } catch (NumberFormatException e) {
                System.out.println("Invalid input. Please enter a number.");
                continue;
            }

            switch (choice) {
                case 1:
                    createAccount();
                    break;
                case 2:
                    performTransaction();
                    break;
                case 3:
                    displayAccountInfo();
                    break;
                case 4:
                    displayAllSummaries();
                    break;
                case 5:
                    System.out.println("Goodbye!");
                    scanner.close();
                    return;
                default:
                    System.out.println("Invalid choice. Please try again.");
            }
        }
    }

    public static void main(String[] args) {
        BankApp app = new BankApp();
        app.showMenu();
    }
}