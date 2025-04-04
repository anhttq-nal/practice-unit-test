# Refactoring OrderProcessingService với Factory Pattern

## Vấn đề ban đầu

Trong phiên bản trước của `OrderProcessingService`, việc xử lý đơn hàng được thực hiện bằng cách sử dụng cấu trúc switch-case trong phương thức `processOrder` và sau đó gọi các phương thức xử lý riêng biệt (`processTypeAOrder`, `processTypeBOrder`, `processTypeCOrder`). Điều này có một số vấn đề:

1. **Khó mở rộng**: Khi thêm một loại đơn hàng mới, chúng ta phải sửa đổi nhiều nơi - cả switch-case và thêm phương thức xử lý mới.
2. **Vi phạm nguyên tắc Open/Closed**: Lớp không được "đóng" đối với sửa đổi khi bổ sung tính năng mới.
3. **Phương thức quá dài**: Các phương thức xử lý có nhiều logic, làm cho lớp trở nên lớn và khó quản lý.

## Áp dụng Factory Pattern

### 1. Tạo interface OrderProcessorInterface

```php
interface OrderProcessorInterface {
    public function process(Order $order, int $userId): void;
}
```

Interface này định nghĩa hành vi chung cho tất cả các processor - xử lý một đơn hàng.

### 2. Tạo các lớp processor cụ thể

- **TypeAOrderProcessor**: Xử lý đơn hàng loại A (xuất file CSV)
- **TypeBOrderProcessor**: Xử lý đơn hàng loại B (gọi API)
- **TypeCOrderProcessor**: Xử lý đơn hàng loại C (cập nhật trạng thái)
- **UnknownTypeOrderProcessor**: Xử lý đơn hàng có loại không xác định

Mỗi lớp chỉ có một trách nhiệm duy nhất - xử lý một loại đơn hàng cụ thể.

### 3. Tạo OrderProcessorFactory

Factory này chịu trách nhiệm tạo ra processor phù hợp dựa trên loại đơn hàng.

```php
class OrderProcessorFactory {
    public function createProcessor(Order $order): OrderProcessorInterface {
        switch ($order->type) {
            case 'A': return new TypeAOrderProcessor();
            // ...
        }
    }
}
```

### 4. Cập nhật OrderProcessingService

Bây giờ OrderProcessingService chỉ cần:
- Lấy danh sách đơn hàng
- Với mỗi đơn hàng, tạo processor thích hợp và gọi phương thức process
- Cập nhật độ ưu tiên và lưu trạng thái

## Lợi ích của việc refactor

1. **Tính mở rộng cao**: Để thêm một loại đơn hàng mới, chỉ cần tạo một lớp processor mới và cập nhật factory.
2. **Phù hợp với Open/Closed Principle**: Lớp OrderProcessingService giờ đã "đóng" đối với sửa đổi, nhưng "mở" cho việc mở rộng.
3. **Trách nhiệm rõ ràng**: Mỗi lớp processor chỉ có một trách nhiệm duy nhất - tuân thủ Single Responsibility Principle.
4. **Dễ test**: Mỗi processor có thể được test độc lập, giúp việc kiểm thử trở nên đơn giản hơn.
5. **Dễ quản lý**: Cấu trúc code rõ ràng, dễ hiểu và dễ bảo trì.

## Kết luận

Việc refactor OrderProcessingService với Factory Pattern đã cải thiện đáng kể chất lượng code theo các nguyên tắc SOLID. Code giờ đây:
- Dễ đọc hơn
- Dễ mở rộng hơn 
- Dễ test hơn
- Và tuân thủ tốt hơn các nguyên tắc thiết kế hướng đối tượng
</rewritten_file> 