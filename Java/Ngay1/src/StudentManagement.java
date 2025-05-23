import java.util.ArrayList;
import java.util.Comparator;
import java.util.Scanner;

public class StudentManagement {
    private ArrayList<Student> students = new ArrayList<>();
    private int nextId = 1; 

    // Thêm sinh viên mới
    public void addStudent(String name, int age, double score) {
        if (age <= 0) {
            System.out.println("Error: Age must be greater than 0.");
            return;
        }
        if (score < 0 || score > 10) {
            System.out.println("Error: Score must be between 0 and 10.");
            return;
        }

        Student student = new Student(name, age, score);
        student.setId(nextId++);
        students.add(student);
        System.out.println("Student added successfully: " + student);
    }

    // Hiển thị danh sách sinh viên
    public void printStudents() {
        if (students.isEmpty()) {
            System.out.println("No students in the list.");
            return;
        }
        System.out.println("List of students:");
        for (Student student : students) {
            System.out.println(student);
        }
    }

    //Hiển thị thông tin 1 sinh viên
    public void printStudent(Student student) {
        System.out.println("Found student: " + student);
    }

    // Tìm sinh viên theo tên (sử dụng contains)
    public void findStudentByName(String keyword) {
        boolean found = false;
        for (Student student : students) {
            if (student.getName().toLowerCase().contains(keyword.toLowerCase())) {
                printStudent(student);
                found = true;
            }
        }
        if (!found) {
            System.out.println("No student found with name containing: " + keyword);
        }
    }

    // Tìm sinh viên có điểm cao nhất
    public void findTopStudent() {
        if (students.isEmpty()) {
            System.out.println("No students in the list.");
            return;
        }
        Student topStudent = students.stream()
                .max(Comparator.comparingDouble(Student::getScore))
                .orElse(null);
        printStudent(topStudent);
    }

    // Sắp xếp danh sách theo điểm giảm dần
    public void sortByScoreDescending() {
        students.sort(Comparator.comparingDouble(Student::getScore).reversed());
        System.out.println("Students sorted by score (descending):");
        printStudents();
    }

    // Tính điểm trung bình
    public double calculateAverageScore() {
        if (students.isEmpty()) {
            return 0.0;
        }
        double sum = 0.0;
        for (Student student : students) {
            sum += student.getScore();
        }
        return sum / students.size();
    }

    // Tính giai thừa của tuổi sinh viên đầu tiên 
    public long calculateFactorialOfFirstStudentAge() {
        if (students.isEmpty()) {
            System.out.println("No students in the list.");
            return 0;
        }
        return factorial(students.get(0).getAge());
    }

    // Hàm đệ quy tính giai thừa
    private long factorial(int n) {
        if (n <= 1) {
            return 1;
        }
        return n * factorial(n - 1);
    }

    public void showMenu() {
        Scanner scanner = new Scanner(System.in);
        while (true) {
            System.out.println("\n=== Student Management System ===");
            System.out.println("1. Add new student");
            System.out.println("2. Display all students");
            System.out.println("3. Find student by name");
            System.out.println("4. Find student with highest score");
            System.out.println("5. Sort students by score (descending)");
            System.out.println("6. Calculate average score");
            System.out.println("7. Calculate factorial of first student's age");
            System.out.println("8. Exit");
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
                    System.out.print("Enter name: ");
                    String name = scanner.nextLine();
                    System.out.print("Enter age: ");
                    int age;
                    try {
                        age = Integer.parseInt(scanner.nextLine());
                    } catch (NumberFormatException e) {
                        System.out.println("Invalid age. Please enter a number.");
                        continue;
                    }
                    System.out.print("Enter score: ");
                    double score;
                    try {
                        score = Double.parseDouble(scanner.nextLine());
                    } catch (NumberFormatException e) {
                        System.out.println("Invalid score. Please enter a number.");
                        continue;
                    }
                    addStudent(name, age, score);
                    break;
                case 2:
                    printStudents();
                    break;
                case 3:
                    System.out.print("Enter name to search: ");
                    String keyword = scanner.nextLine();
                    findStudentByName(keyword);
                    break;
                case 4:
                    findTopStudent();
                    break;
                case 5:
                    sortByScoreDescending();
                    break;
                case 6:
                    double avg = calculateAverageScore();
                    System.out.printf("Average score: %.2f%n", avg);
                    break;
                case 7:
                    long factorial = calculateFactorialOfFirstStudentAge();
                    if (factorial > 0) {
                        System.out.println("Factorial of first student's age: " + factorial);
                    }
                    break;
                case 8:
                    System.out.println("Goodbye!");
                    scanner.close();
                    return;
                default:
                    System.out.println("Invalid choice. Please try again.");
            }
        }
    }

    public static void main(String[] args) {
        StudentManagement system = new StudentManagement();
        system.showMenu();
    }
}